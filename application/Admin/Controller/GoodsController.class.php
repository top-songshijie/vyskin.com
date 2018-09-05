<?php
/**
 * 商品列表
 * Created by PhpStorm.
 * User: Tiger Yang
 * Date: 2017/9/4
 * Time: 14:19
 */

namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class GoodsController extends AdminbaseController{
    protected $goods;
    protected $goods_terms;

    function _initialize() {
        parent::_initialize();
        $this->goods = D("Goods");
        $this->goods_terms = D("GoodsTerm");
    }

    // 后台商品管理列表
    public function index(){
        $this->_lists();
        $this->_getTree();
        $this->display();
    }

    // 商品添加
    public function add(){
        if (IS_POST) {
//            if(empty($_POST['post']['term_id'])){
//                $this->error("请选择分类！");
//            }
            if(!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])){
                foreach ($_POST['photos_url'] as $key=>$url){
                    $photourl=sp_asset_relative_url($url);
                    $_POST['smeta']['photo'][]=array("url"=>$photourl,"alt"=>$_POST['photos_alt'][$key]);
                }
            }
            if (!empty($_POST['attribute_key']) && !empty($_POST['attribute_value']) && !empty($_POST['attribute_price'])){
                foreach ($_POST['attribute_key'] as $k=>$v){
                    $_POST['attribute'][]=array('key'=>$v,'value'=>$_POST['attribute_value'][$k],'price'=>$_POST['attribute_price'][$k]);
                }
            }
            if (!empty($_POST['parameter_key']) && !empty($_POST['parameter_value'])){
                foreach ($_POST['parameter_key'] as $k=>$v){
                    $_POST['parameter'][]=array('key'=>$v,'value'=>$_POST['parameter_value'][$k]);
                }
            }
            $_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);
            $_POST['post']['modified']=date("Y-m-d H:i:s",time());
            $goods=I("post.post");
            $goods['smeta']=json_encode($_POST['smeta']);
            $goods['attribute']=json_encode($_POST['attribute']);
            $goods['parameter']=json_encode($_POST['parameter']);
            $goods['content']=htmlspecialchars_decode($goods['content']);
			$goods['content1']=htmlspecialchars_decode($goods['content1']);
            $result=$this->goods->add($goods);
            if ($result) {
                $this->success("添加成功！");
            } else {
                $this->error("添加失败！");
            }
        }else{

            $terms = $this->goods_terms->order(array("listorder"=>"asc"))->select();
            $term_id = I("get.term",0,'intval');
            $this->_getTermTree();
            $this->level();
            $term=$this->goods_terms->where(array('term_id'=>$term_id))->find();
            $this->assign("term",$term);
            $this->assign("terms",$terms);
            $this->display();
        }
    }

    // 商品编辑
    public function edit(){
        if (IS_POST) {
//            if(empty($_POST['post']['term_id'])){
//                $this->error("请选择分类！");
//            }
            if(!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])){
                foreach ($_POST['photos_url'] as $key=>$url){
                    $photourl=sp_asset_relative_url($url);
                    $_POST['smeta']['photo'][]=array("url"=>$photourl,"alt"=>$_POST['photos_alt'][$key]);
                }
            }
            if (!empty($_POST['attribute_key']) && !empty($_POST['attribute_value']) && !empty($_POST['attribute_price'])){
                foreach ($_POST['attribute_key'] as $k=>$v){
                    $_POST['attribute'][]=array('key'=>$v,'value'=>$_POST['attribute_value'][$k],'price'=>$_POST['attribute_price'][$k]);
                }
            }
            if (!empty($_POST['parameter_key']) && !empty($_POST['parameter_value'])){
                foreach ($_POST['parameter_key'] as $k=>$v){
                    $_POST['parameter'][]=array('key'=>$v,'value'=>$_POST['parameter_value'][$k]);
                }
            }
            $_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);
            $_POST['post']['modified']=date("Y-m-d H:i:s",time());
            $goods=I("post.post");
            $goods['smeta']=json_encode($_POST['smeta']);
            $goods['attribute']=json_encode($_POST['attribute']);
            $goods['content']=htmlspecialchars_decode($goods['content']);
			$goods['contentwap']=htmlspecialchars_decode($goods['contentwap']);
//            dump($goods);exit();
			$result=$this->goods->save($goods);
//            $aa = $this->goods->getLastSql();dump($aa);exit();
            if ($result!==false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }else{
            $id=  I("get.id",0,'intval');
            $terms=$this->goods_terms->select();
            $post=$this->goods->where("id=$id")->find();
            $this->_getTermTree(array($post['term_id']));
            $this->level();
            $this->assign("post",$post);
            $this->assign("smeta",json_decode($post['smeta'],true));
            $this->assign("attribute",json_decode($post['attribute'],true));
            $this->assign("parameter",json_decode($post['parameter'],true));
            $this->assign("terms",$terms);
            $this->assign("term",$post['term_id']);
            $this->display();
        }
    }

    // 商品等级
    public function level() {
        $this->assign("level", array(1 => '娱乐', 2 => '入门', 3 => '中端', 4 => '高端'));
    }

    // 商品排序
    public function listorders() {
        $status = parent::_listorders($this->term_relationships_model);
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }

    /**
     * 商品列表处理方法,根据不同条件显示不同的列表
     * @param array $where 查询条件
     */
    private function _lists($where=array()){
        $term_id=I('request.term',0,'intval');
        
        if(!empty($term_id)){
            $where['term_id']=$term_id;
            $term=$this->goods_terms->where(array('term_id'=>$term_id))->find();
            $this->assign("term",$term);
        }

        $start_time=I('request.start_time');
        if(!empty($start_time)){
            $where['createtime']=array(
                array('EGT',$start_time)
            );
        }

        $end_time=I('request.end_time');
        if(!empty($end_time)){
            if(empty($where['createtime'])){
                $where['createtime']=array();
            }
            array_push($where['createtime'], array('ELT',$end_time));
        }

        $keyword=I('request.keyword');
        if(!empty($keyword)){
            $where['title']=array('like',"%$keyword%");
        }

        $count=$this->goods->where($where)->count('id');

        $page = $this->page($count, 20);

        $posts=$this->goods->field('*,t.name as term_name')->alias('g')
            ->join('cmf_goods_term as t on t.term_id=g.term_id','LEFT')
            ->where($where)
            ->limit($page->firstRow , $page->listRows)
            ->order("createtime DESC")->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign("formget",array_merge($_GET,$_POST));
        $this->assign("posts",$posts);
    }

    // 获取商品分类树结构 select 形式
    private function _getTree(){
        $term_id=empty($_REQUEST['term'])?0:intval($_REQUEST['term']);
        $result = $this->goods_terms->order(array("listorder"=>"asc"))->select();

        $tree = new \Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($result as $r) {
            $r['str_manage'] = '<a href="' . U("GoodsTerm/add", array("parent" => $r['term_id'])) . '">添加子类</a> | <a href="' . U("GoodsTerm/edit", array("id" => $r['term_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("GoodsTerm/delete", array("id" => $r['term_id'])) . '">删除</a> ';
            $r['visit'] = "<a href='#'>访问</a>";
            $r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
            $r['id']=$r['term_id'];
            $r['parentid']=$r['parent'];
            $r['selected']=$term_id==$r['term_id']?"selected":"";
            $array[] = $r;
        }

        $tree->init($array);
        $str="<option value='\$id' \$selected>\$spacer\$name</option>";
        $taxonomys = $tree->get_tree(0, $str);
        $this->assign("taxonomys", $taxonomys);
    }

    // 获取商品分类树结构
    private function _getTermTree($term=array()){
        $result = $this->goods_terms->order(array("listorder"=>"asc"))->select();

        $tree = new \Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($result as $r) {
            $r['str_manage'] = '<a href="' . U("GoodsTerm/add", array("parent" => $r['term_id'])) . '">添加子类</a> | <a href="' . U("GoodsTerm/edit", array("id" => $r['term_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("GoodsTerm/delete", array("id" => $r['term_id'])) . '">删除</a> ';
            $r['visit'] = "<a href='#'>访问</a>";
            $r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
            $r['id']=$r['term_id'];
            $r['parentid']=$r['parent'];
            $r['selected']=in_array($r['term_id'], $term)?"selected":"";
            $r['checked'] =in_array($r['term_id'], $term)?"checked":"";
            $array[] = $r;
        }

        $tree->init($array);
        $str="<option value='\$id' \$selected>\$spacer\$name</option>";
        $taxonomys = $tree->get_tree(0, $str);
        $this->assign("taxonomys", $taxonomys);
    }

    // 商品删除
    public function delete(){
        if(isset($_GET['id'])){
            $id = I("get.id",0,'intval');
            if ($this->goods->where(array('id'=>$id))->delete() !==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }

        if(isset($_POST['ids'])){
            $ids = I('post.ids/a');

            if ($this->goods->where(array('id'=>array('in',$ids)))->delete()!==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

    // 商品审核
    public function check(){
        if(isset($_POST['ids']) && $_GET["check"]){
            $ids = I('post.ids/a');

            if ( $this->goods->where(array('id'=>array('in',$ids)))->save(array('status'=>1)) !== false ) {
                $this->success("审核成功！");
            } else {
                $this->error("审核失败！");
            }
        }
        if(isset($_POST['ids']) && $_GET["uncheck"]){
            $ids = I('post.ids/a');

            if ( $this->goods->where(array('id'=>array('in',$ids)))->save(array('status'=>0)) !== false) {
                $this->success("取消审核成功！");
            } else {
                $this->error("取消审核失败！");
            }
        }
    }

    // 商品置顶
    public function top(){
        if(isset($_POST['ids']) && $_GET["top"]){
            $ids = I('post.ids/a');

            if ( $this->goods->where(array('id'=>array('in',$ids)))->save(array('istop'=>1))!==false) {
                $this->success("置顶成功！");
            } else {
                $this->error("置顶失败！");
            }
        }
        if(isset($_POST['ids']) && $_GET["untop"]){
            $ids = I('post.ids/a');

            if ( $this->goods->where(array('id'=>array('in',$ids)))->save(array('istop'=>0))!==false) {
                $this->success("取消置顶成功！");
            } else {
                $this->error("取消置顶失败！");
            }
        }
    }

    // 商品推荐
    public function recommend(){
        if(isset($_POST['ids']) && $_GET["recommend"]){
            $ids = I('post.ids/a');

            if ( $this->goods->where(array('id'=>array('in',$ids)))->save(array('recommended'=>1))!==false) {
                $this->success("推荐成功！");
            } else {
                $this->error("推荐失败！");
            }
        }
        if(isset($_POST['ids']) && $_GET["unrecommend"]){
            $ids = I('post.ids/a');

            if ( $this->goods->where(array('id'=>array('in',$ids)))->save(array('recommended'=>0))!==false) {
                $this->success("取消推荐成功！");
            } else {
                $this->error("取消推荐失败！");
            }
        }
    }

    // 商品批量移动
    public function move(){
        if(IS_POST){
            if(isset($_GET['ids']) && $_GET['old_term_id'] && isset($_POST['term_id'])){
                $old_term_id=I('get.old_term_id',0,'intval');
                $term_id=I('post.term_id',0,'intval');
                if($old_term_id!=$term_id){
                    $ids=explode(',', I('get.ids/s'));
                    $ids=array_map('intval', $ids);

                    foreach ($ids as $id){
                        $this->term_relationships_model->where(array('object_id'=>$id,'term_id'=>$old_term_id))->delete();
                        $find_relation_count=$this->term_relationships_model->where(array('object_id'=>$id,'term_id'=>$term_id))->count();
                        if($find_relation_count==0){
                            $this->term_relationships_model->add(array('object_id'=>$id,'term_id'=>$term_id));
                        }
                    }

                }

                $this->success("移动成功！");
            }
        }else{
            $tree = new \Tree();
            $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            $terms = $this->goods_terms->order(array("path"=>"ASC"))->select();
            $new_terms=array();
            foreach ($terms as $r) {
                $r['id']=$r['term_id'];
                $r['parentid']=$r['parent'];
                $new_terms[] = $r;
            }
            $tree->init($new_terms);
            $tree_tpl="<option value='\$id'>\$spacer\$name</option>";
            $tree=$tree->get_tree(0,$tree_tpl);

            $this->assign("terms_tree",$tree);
            $this->display();
        }
    }

    // 商品批量复制
    public function copy(){
        if(IS_POST){
            if(isset($_GET['ids']) && isset($_POST['term_id'])){
                $ids=explode(',', I('get.ids/s'));
                $ids=array_map('intval', $ids);
                $term_id=I('post.term_id',0,'intval');
                $term_count=$goods_terms=M('GoodsTerm')->where(array('term_id'=>$term_id))->count();
                if($term_count==0){
                    $this->error('分类不存在！');
                }

                $y=0;
                foreach ($ids as $id){
                    $find_post=$this->goods->field('keywords,content,title,excerpt,smeta,attribute,price')->where(array('id'=>$id))->find();
                    if($find_post){
                        $find_post['term_id']=$term_id;
                        $find_post['createtime']=date('Y-m-d H:i:s');
                        $find_post['modified']=date('Y-m-d H:i:s');
                        $id=$this->goods->add($find_post);
                        if($id){
                            $y++;
                        }
                    }
                }
                if ($y>0) {
                    $this->success("复制成功！");
                } else {
                    $this->error("复制失败！");
                }
            }
        }else{
            $tree = new \Tree();
            $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            $terms = $this->goods_terms->order(array("path"=>"ASC"))->select();
            $new_terms=array();
            foreach ($terms as $r) {
                $r['id']=$r['term_id'];
                $r['parentid']=$r['parent'];
                $new_terms[] = $r;
            }
            $tree->init($new_terms);
            $tree_tpl="<option value='\$id'>\$spacer\$name</option>";
            $tree=$tree->get_tree(0,$tree_tpl);

            $this->assign("terms_tree",$tree);
            $this->display();
        }
    }

    // 商品回收站列表
    public function recyclebin(){
        $this->_lists(array('status'=>array('eq',3)));
        $this->_getTree();
        $this->display();
    }

    // 清除已经删除的商品
    public function clean(){
        if(isset($_POST['ids'])){
            $ids = I('post.ids/a');
            $ids = array_map('intval', $ids);
            $status=$this->goods->where(array("id"=>array('in',$ids),'status'=>3))->delete();
            $this->term_relationships_model->where(array('object_id'=>array('in',$ids)))->delete();

            if ($status!==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }else{
            if(isset($_GET['id'])){
                $id = I("get.id",0,'intval');
                $status=$this->goods->where(array("id"=>$id,'status'=>3))->delete();
                $this->term_relationships_model->where(array('object_id'=>$id))->delete();

                if ($status!==false) {
                    $this->success("删除成功！");
                } else {
                    $this->error("删除失败！");
                }
            }
        }
    }

    // 商品还原
    public function restore(){
        if(isset($_GET['id'])){
            $id = I("get.id",0,'intval');
            if ($this->goods->where(array("id"=>$id,'status'=>3))->save(array("status"=>"1"))) {
                $this->success("还原成功！");
            } else {
                $this->error("还原失败！");
            }
        }
    }
}