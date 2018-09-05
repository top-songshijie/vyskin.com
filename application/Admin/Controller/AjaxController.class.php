<?php
namespace Admin\Controller;

use Common\Controller\NoLimitAdminController;

class AjaxController extends NoLimitAdminController {

    /**
     * 根据单位获取班级
     */
	public function get_school_to_class(){
	    $sid=intval($_POST['sid']);
		$info=M('class')->field('id,name')->where(array('sid'=>$sid,'status'=>'1'))->select();
        $html="<option value='0'>点击选择班级</option>";
		if ($info){
		   foreach ($info as $k=>$v){
               $html.="<option value='".$v['id']."'>".$v['name']."</option>";
           }
        }
        $data['info']=$html;
        $data['status']='success';
        $this->ajaxReturn($data);
	}
	
}