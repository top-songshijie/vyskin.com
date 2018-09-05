<?php
namespace User\Controller;

use Common\Controller\HomebaseController;

class IndexController extends HomebaseController {
    
    // 前台用户首页 (公开)
	public function index() {
	    
		$id=I("get.id",0,'intval');
		
		$users_model=M("Users");
		
		$user=$users_model->where(array("id"=>$id))->find();
		
		if(empty($user)){
			$this->error("查无此人！");
		}
		
		$this->assign($user);
		$this->display(":index");

    }

    //退出
    public function logout(){
    	$ucenter_syn=C("UCENTER_ENABLED");
    	$login_success=false;
    	if($ucenter_syn){
    		include UC_CLIENT_ROOT."client.php";
    		echo uc_user_synlogout();
    	}
    	session("user",null);//只有前台用户退出
    	if(empty($_SESSION['user'])){
            $this->ajaxReturn(array("status"=>1,"info"=>"退出成功"));
        }else{
            $this->ajaxReturn(array("status"=>0,"info"=>"出现错误"));
        }
    }

}
