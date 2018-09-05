<?php
/**
 * Created by PhpStorm.
 * User: 王鹏程
 * Date: 2017/10/16
 * Time: 9:20
 */

namespace Admin\Controller;

use Common\Controller\AppframeController;

class ModuleController extends AppframeController {

    public function action(){
        $where['score'] = array('between', '2000, 9999');
        $fusers = M('users')->field('id')->where($where)->select();
        foreach ($fusers as $k=>$v){
            $fwhere['id'] = $v['id'];
            M('users')->where($fwhere)->setDec('score', 1000);
        }
        $swhere['score'] = array('between', '10000, 29999');
        $fsusers = M('users')->field('id')->where($swhere)->select();
        foreach ($fsusers as $k=>$v){
            $fswhere['id'] = $v['id'];
            M('users')->where($fswhere)->setDec('score', 4000);
        }
        $stwhere['score'] = array('egt', 30000);
        $fstusers = M('users')->field('id')->where($stwhere)->select();
        foreach ($fstusers as $k=>$v){
            $fstwhere['id'] = $v['id'];
            M('users')->where($fstwhere)->setDec('score', 10000);
        }
    }
}