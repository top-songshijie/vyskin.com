<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class MainController extends AdminbaseController {
	
    public function index(){
    	
    	$mysql= M()->query("select VERSION() as version");
    	$mysql=$mysql[0]['version'];
    	$mysql=empty($mysql)?L('UNKNOWN'):$mysql;

    	//server infomaions
    	$info = array(
    			L('OPERATING_SYSTEM') => PHP_OS,
    			L('OPERATING_ENVIRONMENT') => $_SERVER["SERVER_SOFTWARE"],
    	        L('PHP_VERSION') => PHP_VERSION,
    			L('PHP_RUN_MODE') => php_sapi_name(),
				L('PHP_VERSION') => phpversion(),
    			L('MYSQL_VERSION') =>$mysql,
    			L('UPLOAD_MAX_FILESIZE') => ini_get('upload_max_filesize'),
    			L('MAX_EXECUTION_TIME') => ini_get('max_execution_time') . "s",
    			L('DISK_FREE_SPACE') => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
    	);
    	$this->assign('server_info', $info);
//        $count_order=$this->count_order();
//        $count_user=$this->count_user();
//        $count_amount=$this->count_amount();
//        $this->assign('count_order',$count_order);
//        $this->assign('count_user',$count_user);
//        $this->assign('count_amount',$count_amount);
    	$this->display();
    }

    /**
     * 订单统计
     * @return mixed
     */
    protected function count_order($start_time,$end_time){
        $data['all']=M('order')->count('id');//全部订单
        $data['unpay']=M('order')->where("status = 1")->count('id');//未付款订单
        $data['payed']=M('order')->where("status = 2 ")->count('id');//已付款订单
        $data['completed']=M('order')->where("status = 3 ")->count('id');//已完成订单
        return $data;
    }

    /**
     * 会员统计
     * @return mixed
     */
    protected function count_user($start_time,$end_time){
        $data=M('users')->count('id');//全部订单
        return $data;
    }

    /**
     * 订单金额统计(统计已完成订单)
     * @return mixed
     */
    protected function count_amount($start_time='0',$end_time='1501234567'){
        $where['status']=3;
        if(!empty($start_time)){
            $where['addtime']=array(
                array('EGT',$start_time)
            );
        }
        if(!empty($end_time)){
            if(empty($where['addtime'])){
                $where['addtime']=array();
            }
            array_push($where['addtime'], array('ELT',$end_time));
        }
        $data=M('order')->where($where)->sum('price');//全部订单
        if (empty($data)){
            $data=0;
        }
        return $data;
    }
}