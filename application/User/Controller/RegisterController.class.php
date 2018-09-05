<?php
namespace User\Controller;

use Common\Controller\HomebaseController;

/**
* 注册
*/
class RegisterController extends HomebaseController
{
	/**
	 * 注册页面
	 */
	public function register()
	{
		$this->display(":register");
	}

	/**
	 * 注册提交
	 */
	public function doregister()
	{    
        $rules = array(
            //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
            array('user_login','','手机号已被注册！！',0,'unique',1),
            array('user_pass','5,20',"密码长度至少5位，最多20位！",1,'length',3),
        );
        	
	    $users_model=M("Users");
	     
	    if($users_model->validate($rules)->create()===false){
	    	 $this->ajaxReturn(array('code' => 400,'msg' => '手机号已被注册或者密码少于5位'));exit();
	    }
	    $mobile_verify=I('post.mobile_verify');
	    if(empty($mobile_verify)){
	    	$this->ajaxReturn(array('code' => 400,'msg' => '手机验证码不能为空！'));
	        exit();
        }
	    $mobile_verify_zhen=$_SESSION['mobile_verify'];
	    if($mobile_verify!=$mobile_verify_zhen){
	    	$this->ajaxReturn(array('code' => 400,'msg' => '手机验证码错误！'));
	        exit();
        }
	     
	    $user_login=I('post.user_login');
	    $user_pass=I('post.user_pass');	    
	    $data=array(
	        'user_login' => $user_login,
	        'user_email' => '',
	        'mobile' =>'',
	        'user_nicename' =>'',
	        'user_pass' => sp_password($user_pass),
	        'last_login_ip' => get_client_ip(0,true),
	        'create_time' => date("Y-m-d H:i:s"),
	        'last_login_time' => date("Y-m-d H:i:s"),
	        'user_status' => 1,
	        "user_type"=>2,//会员
	    );
	    $result = $users_model->add($data);
	    if($result){
	        $data['id']=$result;
	        session('user',$data);
	        $this->ajaxReturn(array('code' => 200,'msg' => '注册成功'));
	    }else{
	    	$this->ajaxReturn(array('code' => 400,'msg' => '出现错误'));
	    }
	}

	/**
	 * 手机验证码生成及发送
	 * @return [type] [description]
	 */
	public function mobile_verify()
	{
		$user_login=I('post.user_login');
		if(preg_match("/^1[34578]\d{9}$/", $user_login)){
			$mobile_verify = rand(123456, 999999);
			session('mobile_verify',$mobile_verify);
			$res = $this->SendCode($user_login,$mobile_verify);
			if($res == -1){
				$this->ajaxReturn(array("code"=>400,"msg"=>"发送失败"));
			}else{
				$this->ajaxReturn(array("code"=>200,"mobile_verify"=>$mobile_verify,"msg"=>"发送成功"));
			}
		}else{
			$this->ajaxReturn(array("code"=>400,"msg"=>"不是正确的手机格式"));
		}
	}

	/**
	 * 发送手机验证码函数
	 * @return [type] [description]
	 */
	public function SendCode($mobile,$code)
	{
			date_default_timezone_set('PRC');//设置时区
			$url 		= "http://www.ztsms.cn/sendNSms.do";//提交地址
			$username 	= "yiyanjingguanwang";//用户名
			$password 	= "Cxz307312";//原密码
			$data = array(
				'content' 	=> "您的短信验证码为".$code."，请勿将验证码提供给他人【妍依净】",//短信内容
				'mobile' 	=> $mobile,//手机号码
				'productid' => '676767',//产品id
				'xh'		=> ''//小号
		);
			$isTranscoding = false;
			$timeout = 30;
			$data['content'] = $isTranscoding === true ? mb_convert_encoding($data['content'], "UTF-8") : $data['content'];
			$data['username']=$username;
			$data['tkey'] 	= date('YmdHis');
			$data['password'] = md5(md5($password) . $data['tkey']);
			$curl = curl_init();// 启动一个CURL会话
			curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
			curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
			curl_setopt($curl, CURLOPT_POST, true); // 发送一个常规的Post请求
			curl_setopt($curl, CURLOPT_POSTFIELDS,  http_build_query($data)); // Post提交的数据包
			curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); // 设置超时限制防止死循环
			curl_setopt($curl, CURLOPT_HEADER, false); // 显示返回的Header区域内容
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // 获取的信息以文件流的形式返回
			$result = curl_exec($curl); // 执行操作
		if (curl_errno($curl)){
			echo 'Error POST'.curl_error($curl);
		}
		return $result;
	}

	/**
	 * 忘记密码页面
	 */
	public function forgot_password()
	{
		$this->display(":forgot_password");
	}

	// 前台用户忘记密码提交(手机方式找回)
	public function do_mobile_forgot_password(){
	    if(IS_POST){	    
    	    $mobile_verify=I('post.mobile_verify');
    	    if(empty($mobile_verify)) {
    	    	$this->ajaxReturn(array("code"=>400,"msg"=>"手机验证码不能为空"));
    	    	exit();
    	    }
		    $mobile_verify_zhen=$_SESSION['mobile_verify'];
		    if($mobile_verify!=$mobile_verify_zhen){
		    	$this->ajaxReturn(array("code"=>400,"msg"=>"手机验证码错误！"));
		        exit();
	        }
    	     
            $rules = array(
                //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
                array('user_login', 'require', '手机号不能为空！', 1 ),
                array('user_pass','require','密码不能为空！',1),
                array('user_pass','5,20',"密码长度至少5位，最多20位！",1,'length',3),
                array('user_pass','reuser_pass','两次输入密码不一致',0,'confirm'),
            );
            	
    	    $users_model=M("Users");
    	     
    	    if($users_model->validate($rules)->create()===false){
    	        $this->ajaxReturn(array("code"=>400,"msg"=>"验证错误"));
    	    }
    	     
    	    $password=I('post.user_pass');
    	    $user_login=I('post.user_login');
    	     
    	    $where['user_login']=$user_login;
    	     
    	    $users_model=M("Users");
    	    $result = $users_model->where($where)->count();
    	    if($result){
    	       $result=$users_model->where($where)->save(array('user_pass' => sp_password($password)));
    	       if($result!==false){
    	       	 $this->ajaxReturn(array("code"=>200,"msg"=>"密码重置成功！"));
    	       }else{
    	       	 $this->ajaxReturn(array("code"=>400,"msg"=>"密码重置失败！"));
    	       }
    	    }else{
    	    	 $this->ajaxReturn(array("code"=>400,"msg"=>"该手机号未注册！"));
    	    }
    	}
	}
}