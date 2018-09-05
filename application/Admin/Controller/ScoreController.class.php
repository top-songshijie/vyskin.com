<?php
/**
 * 积分设置
 * Created by PhpStorm.
 * User: Yiku
 * Date: 2017/9/30
 * Time: 09:50
 */

namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class ScoreController extends AdminbaseController{
    protected $score;
    protected $level;

    function _initialize() {
        parent::_initialize();
        $this->score = D("Score");
        $this->level = D("Level");
    }

    // 积分设置管理
    public function index(){
        $this->_lists();
        $this->display();
    }

    // 积分等级管理
    public function level(){
        $this->_levellists();
        $this->display();
    }

    // 积分设置添加
    public function add(){
        if (IS_POST) {
            $post=I("post.post");
            //var_dump($score);exit;
            $score['price']=$_POST['post']['price'];
            $score['score']=$_POST['post']['score'];
            $score['ctime'] = date("Y-m-d H:i:s", time());
            $result=$this->score->add($score);
            if ($result) {
                $this->success("添加成功！");
            } else {
                $this->error("添加失败！");
            }
        }else{
            $score=$this->score->find();
            $this->assign("score",$score);
            $this->display();
        }
    }

    // 积分设置添加
    public function level_add(){
        if (IS_POST) {
            $post=I("post.post");
            $level['level']=$_POST['post']['level'];
            $level['min']=$_POST['post']['min'];
            $level['max']=$_POST['post']['max'];
            $level['valid']=$_POST['post']['valid'];
            $level['ctime'] = date("Y-m-d H:i:s", time());
            $result=$this->level->add($level);
            if ($result) {
                $this->success("添加成功！");
            } else {
                $this->error("添加失败！");
            }
        }else{
            $level=$this->level->find();
            $this->assign("level",$level);
            $this->display();
        }
    }

    // 积分编辑
    public function edit(){
        if (IS_POST) {
            $post=I("post.post");
            $score['price']=$_POST['post']['price'];
            $score['score']=$_POST['post']['score'];
            $result=$this->score->save($score);
            if ($result!==false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }else{
            $id=  I("get.id",0,'intval');
            $post=$this->score->where("id=$id")->find();
            $this->assign("post",$post);
            $this->display();
        }
    }

    // 积分等级编辑
    public function level_edit(){
        if (IS_POST) {
            $post=I("post.post");
            $level['level']=$_POST['post']['level'];
            $level['min']=$_POST['post']['min'];
            $level['max']=$_POST['post']['max'];
            $level['valid']=$_POST['post']['valid'];
            $result=$this->level->save($level);
            if ($result!==false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }else{
            $id=  I("get.id",0,'intval');
            $post=$this->level->where("id=$id")->find();
            $this->assign("post",$post);
            $this->display();
        }
    }

    /**
     * 积分列表处理方法,根据不同条件显示不同的列表
     * @param array $where 查询条件
     */
    private function _lists($where=array()){
        $score=$this->score->find();
        $this->assign("score",$score);

        $start_time=I('request.start_time');
        if(!empty($start_time)){
            $where['ctime']=array(
                array('EGT',$start_time)
            );
        }

        $end_time=I('request.end_time');
        if(!empty($end_time)){
            if(empty($where['ctime'])){
                $where['ctime']=array();
            }
            array_push($where['ctime'], array('ELT',$end_time));
        }

        $keyword=I('request.keyword');
        if(!empty($keyword)){
            $where['price']=array('like',"%$keyword%");
        }

        $count=$this->score->where($where)->count('id');

        $page = $this->page($count, 20);

        $posts=$this->score->field('*')
            ->where($where)
            ->limit($page->firstRow , $page->listRows)
            ->order("ctime DESC")->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign("formget",array_merge($_GET,$_POST));
        $this->assign("posts",$posts);
    }

    /**
     * 积分等级处理方法,根据不同条件显示不同的列表
     * @param array $where 查询条件
     */
    private function _levellists($where=array()){
        $level=$this->level->find();
        $this->assign("level",$level);

        $start_time=I('request.start_time');
        if(!empty($start_time)){
            $where['ctime']=array(
                array('EGT',$start_time)
            );
        }

        $end_time=I('request.end_time');
        if(!empty($end_time)){
            if(empty($where['ctime'])){
                $where['ctime']=array();
            }
            array_push($where['ctime'], array('ELT',$end_time));
        }

        $keyword=I('request.keyword');
        if(!empty($keyword)){
            $where['level']=array('like',"%$keyword%");
            $where['valid']=array('like',"%$keyword%");
        }

        $count=$this->level->where($where)->count('id');

        $page = $this->page($count, 20);

        $posts=$this->level->field('*')
            ->where($where)
            ->limit($page->firstRow , $page->listRows)
            ->order("ctime DESC")->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign("formget",array_merge($_GET,$_POST));
        $this->assign("posts",$posts);
    }

    // 积分删除
    public function delete(){
        if(isset($_GET['id'])){
            $id = I("get.id",0,'intval');
            if ($this->score->where(array('id'=>$id))->delete() !==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }

        if(isset($_POST['ids'])){
            $ids = I('post.ids/a');

            if ($this->score->where(array('id'=>array('in',$ids)))->delete()!==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }
    // 积分等级删除
    public function level_delete(){
        if(isset($_GET['id'])){
            $id = I("get.id",0,'intval');
            if ($this->level->where(array('id'=>$id))->delete() !==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }

        if(isset($_POST['ids'])){
            $ids = I('post.ids/a');

            if ($this->level->where(array('id'=>array('in',$ids)))->delete()!==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }
}