<?php
namespace Common\Controller;
use Common\Controller\AppframeController;

class NoLimitController extends AppframeController {

    public function __construct() {
        $this->set_action_success_error_tpl();
        parent::__construct();
    }

    function _initialize() {
        parent::_initialize();
        defined('TMPL_PATH') or define("TMPL_PATH", C("SP_TMPL_PATH"));
    }

    /**
     * 加载模板和页面输出 可以返回输出内容
     * @access public
     * @param string $templateFile 模板文件名
     * @param string $charset 模板输出字符集
     * @param string $contentType 输出类型
     * @param string $content 模板输出内容
     * @return mixed
     */
    public function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
        parent::display($this->parseTemplate($templateFile), $charset, $contentType,$content,$prefix);
    }

    /**
     * 获取输出页面内容
     * 调用内置的模板引擎fetch方法，
     * @access protected
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $content 模板输出内容
     * @param string $prefix 模板缓存前缀*
     * @return string
     */
    public function fetch($templateFile='',$content='',$prefix=''){
        $templateFile = empty($content)?$this->parseTemplate($templateFile):'';
        return parent::fetch($templateFile,$content,$prefix);
    }

    /**
     * 自动定位模板文件
     * @access protected
     * @param string $template 模板文件规则
     * @return string
     */
    public function parseTemplate($template='') {

        $tmpl_path=C("SP_TMPL_PATH");
        define("SP_TMPL_PATH", $tmpl_path);
        if($this->theme) { // 指定模板主题
            $theme = $this->theme;
        }else{
            // 获取当前主题名称
            $theme      =    C('SP_DEFAULT_THEME');
            if(C('TMPL_DETECT_THEME')) {// 自动侦测模板主题
                $t = C('VAR_TEMPLATE');
                if (isset($_GET[$t])){
                    $theme = $_GET[$t];
                }elseif(cookie('think_template')){
                    $theme = cookie('think_template');
                }
                if(!file_exists($tmpl_path."/".$theme)){
                    $theme  =   C('SP_DEFAULT_THEME');
                }
                cookie('think_template',$theme,864000);
            }
        }

        $theme_suffix="";

        if(C('MOBILE_TPL_ENABLED') && sp_is_mobile()){//开启手机模板支持

            if (C('LANG_SWITCH_ON',null,false)){
                if(file_exists($tmpl_path."/".$theme."_mobile_".LANG_SET)){//优先级最高
                    $theme_suffix  =  "_mobile_".LANG_SET;
                }elseif (file_exists($tmpl_path."/".$theme."_mobile")){
                    $theme_suffix  =  "_mobile";
                }elseif (file_exists($tmpl_path."/".$theme."_".LANG_SET)){
                    $theme_suffix  =  "_".LANG_SET;
                }
            }else{
                if(file_exists($tmpl_path."/".$theme."_mobile")){
                    $theme_suffix  =  "_mobile";
                }
            }
        }else{
            $lang_suffix="_".LANG_SET;
            if (C('LANG_SWITCH_ON',null,false) && file_exists($tmpl_path."/".$theme.$lang_suffix)){
                $theme_suffix = $lang_suffix;
            }
        }

        $theme=$theme.$theme_suffix;

        C('SP_DEFAULT_THEME',$theme);

        $current_tmpl_path=$tmpl_path.$theme."/";
        // 获取当前主题的模版路径
        define('THEME_PATH', $current_tmpl_path);

        $cdn_settings=sp_get_option('cdn_settings');
        if(!empty($cdn_settings['cdn_static_root'])){
            $cdn_static_root=rtrim($cdn_settings['cdn_static_root'],'/');
            C("TMPL_PARSE_STRING.__TMPL__",$cdn_static_root."/".$current_tmpl_path);
            C("TMPL_PARSE_STRING.__PUBLIC__",$cdn_static_root."/public");
            C("TMPL_PARSE_STRING.__WEB_ROOT__",$cdn_static_root);
        }else{
            C("TMPL_PARSE_STRING.__TMPL__",__ROOT__."/".$current_tmpl_path);
        }


        C('SP_VIEW_PATH',$tmpl_path);
        C('DEFAULT_THEME',$theme);

        define("SP_CURRENT_THEME", $theme);

        if(is_file($template)) {
            return $template;
        }
        $depr       =   C('TMPL_FILE_DEPR');
        $template   =   str_replace(':', $depr, $template);

        // 获取当前模块
        $module   =  MODULE_NAME;
        if(strpos($template,'@')){ // 跨模块调用模版文件
            list($module,$template)  =   explode('@',$template);
        }

        $module =$module."/";

        // 分析模板文件规则
        if('' == $template) {
            // 如果模板文件名为空 按照默认规则定位
            $template = CONTROLLER_NAME . $depr . ACTION_NAME;
        }elseif(false === strpos($template, '/')){
            $template = CONTROLLER_NAME . $depr . $template;
        }

        $file = sp_add_template_file_suffix($current_tmpl_path.$module.$template);
        $file= str_replace("//",'/',$file);
        if(!file_exists_case($file)) E(L('_TEMPLATE_NOT_EXIST_').':'.$file);
        return $file;
    }

    /**
     * 设置错误，成功跳转界面
     */
    private function set_action_success_error_tpl(){
        $theme      =    C('SP_DEFAULT_THEME');
        if(C('TMPL_DETECT_THEME')) {// 自动侦测模板主题
            if(cookie('think_template')){
                $theme = cookie('think_template');
            }
        }
        //by ayumi手机提示模板
        $tpl_path = '';
        if(C('MOBILE_TPL_ENABLED') && sp_is_mobile() && file_exists(C("SP_TMPL_PATH")."/".$theme."_mobile")){//开启手机模板支持
            $theme  =   $theme."_mobile";
            $tpl_path=C("SP_TMPL_PATH").$theme."/";
        }else{
            $tpl_path=C("SP_TMPL_PATH").$theme."/";
        }

        //by ayumi手机提示模板
        $defaultjump=THINK_PATH.'Tpl/dispatch_jump.tpl';
        $action_success = sp_add_template_file_suffix($tpl_path.C("SP_TMPL_ACTION_SUCCESS"));
        $action_error = sp_add_template_file_suffix($tpl_path.C("SP_TMPL_ACTION_ERROR"));
        if(file_exists_case($action_success)){
            C("TMPL_ACTION_SUCCESS",$action_success);
        }else{
            C("TMPL_ACTION_SUCCESS",$defaultjump);
        }

        if(file_exists_case($action_error)){
            C("TMPL_ACTION_ERROR",$action_error);
        }else{
            C("TMPL_ACTION_ERROR",$defaultjump);
        }
    }


}