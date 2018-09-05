<?php
namespace User\Controller;

use Common\Controller\HomebaseController;

/**
* 忘记密码
*/
class ForgetpasswordController extends HomebaseController
{
	/**
	 * 忘记密码页面
	 */
	public function index()
	{
		$this->display(":forget_password");
	}

	// 前台用户忘记密码提交
	public function do_forgot_password()
	{

	    if(IS_POST){
	    	$mobile_verify=I('post.mobile_verify');
		    $mobile_verify_zhen=$_SESSION['mobile_verify'];
		    if($mobile_verify!=$mobile_verify_zhen){
		    	$this->ajaxReturn(array('status' => 0,'info' => '手机验证码错误！'));
		        exit();
	        }
            $rules = array(
                //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
                array('user_login', 'require', '手机号不能为空！', 1 ),
                array('user_pass','require','密码不能为空！',1),
                array('user_pass','5,20',"密码长度至少5位，最多20位！",1,'length',3),
                array('user_pass','reuser_pass','两次输入密码不一致',0,'confirm'),
            );
            //验证规则	
    	    $users_model=M("Users");   	     
    	    if($users_model->validate($rules)->create()===false){
    	        $this->ajaxReturn(array("status"=>0,"info"=>$users_model->getError()));
    	        exit();
    	    }  	     
    	    //查询手机号是否被注册过，以及提交重置
    	    $password=I('post.user_pass');
    	    $user_login=I('post.user_login');    	     
    	    $where['user_login']=$user_login;
    	    $result = $users_model->where($where)->count();
    	    if($result){
    	       $result=$users_model->where($where)->save(array('user_pass' => sp_password($password)));
    	       if($result!==false){
    	       		$this->ajaxReturn(array("status"=>1,"info"=>"密码重置成功"));
    	       }else{
    	       	   $this->ajaxReturn(array("status"=>0,"info"=>"出现错误"));
    	       }
    	    }else{
    	    	$this->ajaxReturn(array("status"=>0,"info"=>"该手机号未注册"));
    	    }
    	}
	}
}