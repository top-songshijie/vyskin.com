<?php
namespace User\Controller;

use Common\Controller\HomebaseController;

class LogoutController extends HomebaseController {
    //退出
    public function index(){
    	$ucenter_syn=C("UCENTER_ENABLED");
    	$login_success=false;
    	if($ucenter_syn){
    		include UC_CLIENT_ROOT."client.php";
    		echo uc_user_synlogout();
    	}
    	session("user",null);//只有前台用户退出
    	if(empty($_SESSION['user'])){
            header("Location:http://vyskin.com");
        }else{
             $this->success('网络错误');
        }
    }

}