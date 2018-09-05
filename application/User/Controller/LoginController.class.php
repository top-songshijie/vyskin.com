<?php
namespace User\Controller;

use Common\Controller\HomebaseController;

/**
* 登录
*/
class LoginController extends HomebaseController
{
	/**
	 * 登录页面
	 * @return [type] [description]
	 */
	public function login()
	{
	    $data = $this->rememberPassword();
	    if($data != 0){
	        $this->assign('data',$data);
        }
		$this->display(":login");
	}

    /**
     * 检测是否记住了密码
     */
    public function rememberPassword()
    {
        $user_login = cookie('user_login');
        $password = cookie('password');
        if(empty($user_login) || empty($password)) {
            return 0;
        }else{
            return array("user_login"=>$user_login,"password"=>$password);
        }
    }

	/**
	 * 登录提交(pc)
	 * @return [type] [description]
	 */
	public function dologin()
	{
    	if(!sp_check_verify_code()){
            $this->ajaxReturn(array("code"=>400,"msg"=>"验证码错误"));exit();
        }
        $users_model = M('Users');
        $user_login = I('post.user_login');
        $where = array(
            "user_status" => 1,
            'user_login' => $user_login,
        );
        $result = $users_model->where($where)->find();       
        if(!empty($result)){
            $password=I('post.user_pass');
            if(sp_compare_password($password, $result['user_pass'])){
                session('user',$result);
                $rempwd=I('post.rempwd','');
                if(!empty($rempwd)) {
                   cookie('user_login',I('post.user_login'),2592000);
                   cookie('password',I('post.user_pass'),2592000);    

                }else{
                   cookie('user_login',null);
                   cookie('password',null);

                }                
                //写入此次登录信息
                $data = array(
                    'last_login_time' => date("Y-m-d H:i:s"),
                    'last_login_ip' => get_client_ip(0,true),
                );
                $users_model->where(array('id'=>$result["id"]))->save($data);
                $this->ajaxReturn(array("code"=>200,"msg"=>"登录成功"));
            }else{
                $this->ajaxReturn(array("code"=>400,"msg"=>"密码错误"));
            }
        }else{
            $this->ajaxReturn(array("code"=>400,"msg"=>"用户名不存在或已被拉黑"));
        }
	}

    /**
     * 登录提交(pc)
     * @return [type] [description]
     */
    public function doLoginPhone()
    {
        $users_model = M('Users');
        $user_login = I('post.user_login');
        $where = array(
            "user_status" => 1,
            'user_login' => $user_login,
        );
        $result = $users_model->where($where)->find();
        if(!empty($result)){
            $password=I('post.user_pass');
            if(sp_compare_password($password, $result['user_pass'])){
                session('user',$result);
                $rempwd=I('post.rempwd','');
                if(!empty($rempwd)) {
                    cookie('user_login',I('post.user_login'),2592000);
                    cookie('password',I('post.user_pass'),2592000);

                }else{
                    cookie('user_login',null);
                    cookie('password',null);

                }
                //写入此次登录信息
                $data = array(
                    'last_login_time' => date("Y-m-d H:i:s"),
                    'last_login_ip' => get_client_ip(0,true),
                );
                $users_model->where(array('id'=>$result["id"]))->save($data);
                $this->ajaxReturn(array("code"=>200,"msg"=>"登录成功"));
            }else{
                $this->ajaxReturn(array("code"=>400,"msg"=>"密码错误"));
            }
        }else{
            $this->ajaxReturn(array("code"=>400,"msg"=>"用户名不存在或已被拉黑"));
        }
    }

}
