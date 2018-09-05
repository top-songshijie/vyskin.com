<?php
namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
* 个人中心
*/
class MineController extends HomebaseController
{
	
	/**
	 * 个人中心首页
	 */
	public function index()
	{
		//添加参数设置
		$loc = I('loc','');
		if(!empty($loc)){
			$id = I('id','');
			if(!empty($id)){
				$this->assign('id',$id);//购物车id，返回刷新使用
			}
			$this->assign('loc',$loc);
		}
		//编辑参数设置
		$locedit = I('locedit','');
		if(!empty($locedit)){
			$id = I('id','');
			if(!empty($id)){
				$this->assign('id',$id);//购物车id，返回刷新使用
			}
			$address_id = I('address_id','');//地址id，修改地址使用
			if(!empty($address_id)){
				$info = M('user_address')->where(array('id'=>$address_id))->find();
				$this->assign('sh_name',$info['sh_name']);
				$this->assign('sh_mobile',$info['sh_mobile']);
				$this->assign('sheng',$info['sheng']);
				$this->assign('shi',$info['shi']);
				$this->assign('qu',$info['qu']);
				$this->assign('xiangxi',$info['xiangxi']);
				$this->assign('locedit',$locedit);	
				$this->assign('address_id',$address_id);	
			}
		}
		//查询是否登录
        $userid = $_SESSION['user']['id'];
        if(empty($userid)){
            $this->error('即将跳转到登录页面',U('User/Login/login'));exit();  
        }
        //收货地址
        $list_address = $this->myaddress();
        //我的信息
        $mymessage = $this->getMymessage();
        //全部
        $list = $this->orderList();
        //未支付
        $count1 = M('order')->where(array('user_id'=>$userid,'status'=>1))->count();
        $list1 = $this->orderList(1);
        //已支付
        $count2 = M('order')->where(array('user_id'=>$userid,'status'=>2))->count();
        $list2 = $this->orderList(2);
        //已完成
        $count3 = M('order')->where(array('user_id'=>$userid,'status'=>3))->count();
        $list3 = $this->orderList(3);
        //已取消
        $count4 = M('order')->where(array('user_id'=>$userid,'status'=>4))->count();
        $list4 = $this->orderList(4);
        // dump($list_address);
        $this->assign('mymessage',$mymessage);
        $this->assign('list_address',$list_address);
        $this->assign('count1',$count1);
        $this->assign('count2',$count2);
        $this->assign('count3',$count3);
        $this->assign('count4',$count4);
        $this->assign('list',$list);
        $this->assign('list1',$list1);
        $this->assign('list2',$list2);
        $this->assign('list3',$list3);
        $this->assign('list4',$list4);
        // dump($list);exit();
        $this->display();
	}

    public function orderListPhone()
    {
        //查询是否登录
        $userid = $_SESSION['user']['id'];
        if(empty($userid)){
            $this->error('请登陆后操作',U('User/Login/login'));exit();
        }

        //全部
        $list = $this->orderList();
        //未支付
        $list1 = $this->orderList(1);
        //已支付
        $list2 = $this->orderList(2);
        //已完成
        $list3 = $this->orderList(3);
        //已取消
        $list4 = $this->orderList(4);

        $this->assign('list',$list);
        $this->assign('list1',$list1);
        $this->assign('list2',$list2);
        $this->assign('list3',$list3);
        $this->assign('list4',$list4);
//         dump($list);exit();
        $this->display('Mine/orderListPhone');
    }


    /**
     * 根据不同的状态查询订单
     * 1=>未支付，2=>已支付，3=>已完成，4=>已取消，
     */
    public function orderList($status="")
    {
        $userid = $_SESSION['user']['id'];
        if(empty($userid)){
            $this->ajaxReturn(array('code'=>400,'msg'=>'没有登录'));exit();
        }
        //查询状态订单
        $status = $status;
        if(!empty($status)){
            $where['status'] = $status;
        }
        $where['user_id'] = $userid;
        //查询订单表
        $order=M('order')->field('status,order_sn,addtime,price')->where($where)->order('id desc')->select();
        //查询订单详情表
        $arr_status = array("1"=>"未支付","2"=>"已支付","3"=>"已完成","4"=>"已取消");
        foreach ($order as $k=>$v){
            $order[$k]['status'] = $arr_status[$order[$k]['status']];
            $order[$k]['addtime'] = date("Y-m-d",$order[$k]['addtime']);
            $order_detail=M('order_detail as od')
                ->field('g.smeta,g.title,od.attribute,od.number,od.price')
                ->join('cmf_goods as g on od.good_id=g.id')
                ->where(array('od.order_sn'=>$v['order_sn']))
                ->select();
            foreach ($order_detail as $key => $value) {
                $order_detail[$key]['smeta'] = json_decode($order_detail[$key]['smeta']);
                $order_detail[$key]['smeta'] = $order_detail[$key]['smeta']->thumb;
                $order_detail[$key]['attribute'] = json_decode($order_detail[$key]['attribute']);
                $order_detail[$key]['attribute'] = $order_detail[$key]['attribute'][1];
                $order[$k]['num'] = $key+1;
            }
            $order[$k]['detail']=$order_detail;
        }
       return $order;
    }

	/**
	 * 我的收货地址
	 */
	public function myaddress()
	{
        $userid = $_SESSION['user']['id'];
        if(empty($userid)){
            $this->ajaxReturn(array('code'=>400,'msg'=>'没有登录'));exit();
        }
	    $list=M('user_address')->where("userid=$userid")->select();
        $mraddress_id = M('users')->where("id=$userid")->getField('address_id');
        foreach ($list as $key => $value) {
            if($value['id']==$mraddress_id) {
                $list[$key]['status'] = 1;
            }else {
                $list[$key]['status'] = 0;
            }
        }
        return $list;
	}

    /**
     * 添加收货地址/编辑收货地址
     */
    public function addMyaddress()
    {
        //用户id
       $userid = $_SESSION['user']['id'];
        if(empty($userid)){
            $this->ajaxReturn(array('code'=>400,'msg'=>'没有登录'));exit();
        }
        //地址id
        $address_id = I('address_id');
        $data['userid']=$userid;
        //省
        $data['sheng']=I('post.sheng');
        //市
        $data['shi']=I('post.shi');
        //区
        $data['qu']=I('post.qu');
        //详细地址
        $data['xiangxi']=I('post.xiangxi');
        //联系人姓名
        $data['sh_name']=I('post.sh_name');
        //收货人手机号
        $data['sh_mobile']=I('post.sh_mobile');
        if(empty($address_id)){
            $res=M('user_address')->data($data)->add();
        }else{
            $res=M('user_address')->where(array("id"=>$address_id))->data($data)->save();
        }
        if($res){
            $this->ajaxReturn(array('code'=>200,'msg'=>'操作成功,请刷新查看！'));
        }else{
            $this->ajaxReturn(array('code'=>400,'msg'=>'出现错误'));
        }
    }


    /**
     * 删除收货地址
     */
    public function delMyaddress()
    {
        //用户id
       $userid = $_SESSION['user']['id'];
        if(empty($userid)){
            $this->error('没有登录');exit();
        }
        $id = I('id','');
        if(empty($id)) {
            $this->error('缺少地址id');exit();
        }
        $res=M('user_address')->where("id=$id")->delete();
        if($res){
            $this->success();
        }else{
            $this->error('出现错误');exit();
        }
    }

    /**
     * 设为默认收货地址
     */
    public function addMraddress()
    {
        //用户id
       $userid = $_SESSION['user']['id'];
        if(empty($userid)){
            $this->ajaxReturn(array('code'=>400,'msg'=>'没有登录'));exit();
        }
        $id = I('id','');
        if(empty($id)) {
            $this->ajaxReturn(array('code'=>400,'msg'=>'缺少地址id'));exit();
        }
        $detail = M('user_address')->where("id=$id")->find();
        $data['sheng'] = $detail['sheng'];
        $data['shi'] = $detail['shi'];
        $data['qu'] = $detail['qu'];
        $data['xiangxi'] = $detail['xiangxi'];
        $data['sh_name'] = $detail['sh_name'];
        $data['sh_mobile'] = $detail['sh_mobile'];
        $data['address_id'] = $id;
        $res = M('users')->where("id=$userid")->data($data)->save();
        if($res) {
            $this->ajaxReturn(array('code'=>200,'msg'=>'修改成功'));
        }else{
            $this->ajaxReturn(array('code'=>400,'msg'=>'出现错误'));
        }
    }
	
	 /**
     * 查看编辑收货地址
     */
    public function getMyaddress()
    {
        $id = I('id','');
		if(empty($id)){
			$this->ajaxReturn(array('code'=>400,'msg'=>'没有地址id'));exit();
		}
		$info = M('user_address')->where(array('id'=>$id))->find();
		if($info){
			$this->ajaxReturn(array('code'=>200,'data'=>$info));
		}else{
			$this->ajaxReturn(array('code'=>400,'msg'=>'没有查询到地址信息'));
		}
		
    }



    /**
     * 获取个人信息
     */
    public function getMymessage()
    {
        $userid = $_SESSION['user']['id'];
        if (empty($userid)) {
            $this->error('没有登录');exit();
        }
        $users=M('users')->field("id,user_login,user_nicename,avatar")->where("id=$userid")->find();
        if (!empty($users)) {
            return $users;
        } else {
            $this->error('出现错误');exit();
        }
    }

    /**
     * 编辑个人信息
     */
    public function editMymessage()
    {
        $userid = $_SESSION['user']['id'];
        if (empty($userid)) {
            $this->error('没有登录！');exit();
        }
        $data['user_login'] = I('user_login','');
        $data['user_nicename'] = I('user_nicename','');
        if(empty($data['user_login']) || empty($data['user_nicename'])){
            $this->error('昵称和手机号不能为空！');exit();
        }
        //上传图片
        if($_FILES['avatar']!=""){
            //图片上传
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  = './'.C("UPLOADPATH");
            $upload->savePath  =      './ueditor/'; // 设置附件上传目录
            // 上传单个文件
            $info = $upload->uploadOne($_FILES['avatar']);
            if(!$info) {// 上传错误提示错误信息
//                $this->error($upload->getError());exit();
            }else{// 上传成功 获取上传文件信息
                $avatar=$info['savepath'].$info['savename'];
                $avatar=substr($avatar, 1);
                $data['avatar']="/data/upload".$avatar;
            }
        }
        $res = M('users')->data($data)->where("id=$userid")->save();
        if ($res) {
            $this->success('修改成功！');
        } else {
            $this->error('出现错误！');
        }
    }
	
	
	//地址列表（手机端）
	public function getAddressListPhone()
	{
		$location = I('location','');
		$id = I('id','');//返回刷新参数
		$myaddress = $this->myaddress();
		if($location =='order' || !empty($id)){
			$location = 1;
			$this->assign('location',$location);
			$this->assign('id',$id);
		}
        if($location =='orderzhijie'){
            $location = 2;
            $this->assign('location',$location);
        }
//		dump($location);exit();
		$this->assign('myaddress',$myaddress);
		$this->display('Mine/getAddressListPhone');
	}
	
	//添加地址可设置为默认地址（手机端）
	public function addAddressPhone()
	{
		if(IS_POST){

			$userid = $_SESSION['user']['id'];
			if (empty($userid)) {
				$this->ajaxReturn(array('code'=>400,'msg'=>'没有登录！'));exit();
			}
			$sh_name = I('sh_name','');
			$sh_mobile = I('sh_mobile','');
			$sheng = I('sheng','');
			$shi = I('shi','');
			$qu = I('qu','');
			$xiangxi = I('xiangxi','');
			$status = I('status','');
			
			if(empty($sh_name)){
				$this->ajaxReturn(array('code'=>400,'msg'=>'请填写收货人姓名！'));exit();
			}
			if(empty($sh_mobile)){
				$this->ajaxReturn(array('code'=>400,'msg'=>'请填写收货人手机号！'));exit();
			}
			if(empty($sheng) || empty($shi) || empty($qu) || empty($xiangxi)){
				$this->ajaxReturn(array('code'=>400,'msg'=>'请将收货地址填写完整！'));exit();
			}
			if($status==''){
				$this->ajaxReturn(array('code'=>400,'msg'=>'不知道是否应设置为默认地址！'));exit();
			}
			$data1 = array(
				'sh_name' => $sh_name,
				'sh_mobile' => $sh_mobile,
				'sheng' => $sheng,
				'shi' => $shi,
				'qu' => $qu,
				'xiangxi' => $xiangxi,
				'userid' => $userid,
			);
			$res1 = M('user_address')->data($data1)->add();
			if($status == 1){
				$data2 = array(
					'sh_name' => $sh_name,
					'sh_mobile' => $sh_mobile,
					'sheng' => $sheng,
					'shi' => $shi,
					'qu' => $qu,
					'xiangxi' => $xiangxi,
					'address_id' => $res1,
				);
				$res2 = M('users')->where(array('id'=>$userid))->data($data2)->save();
				if(!$res1){
					$this->ajaxReturn(array('code'=>400,'msg'=>'添加失败！'));exit();
				}
				if(!$res2){
					$this->ajaxReturn(array('code'=>400,'msg'=>'设置为默认地址失败！'));exit();
				}
				$this->ajaxReturn(array('code'=>200,'msg'=>'操作成功！'));exit();
			}else{
				if(!$res1){
					$this->ajaxReturn(array('code'=>400,'msg'=>'添加失败！'));exit();
				}

				$this->ajaxReturn(array('code'=>200,'msg'=>'操作成功！'));exit();
			}
			

		
		}else{
            $xinjianbiaoji = I('xinjianbiaoji','');
		    if(!empty($xinjianbiaoji)){
		        $id = I('id','');
		        if(!empty($id)){
		            $this->assign('xinjianbiaoji',$xinjianbiaoji);
		            $this->assign('id',$id);
                }else{
                    $this->assign('xinjianbiaoji',$xinjianbiaoji);
                }
            }
			$this->display('Mine/addAddressPhone');
		}
	}

	
	
	//编辑个人信息显示，操作和pc端公用一个（手机端）
	public function editMyMessagePhone()
	{
		$userid = $_SESSION['user']['id'];
		if (empty($userid)) {
			$this->ajaxReturn(array('code'=>400,'msg'=>'没有登录！'));exit();
		}
		$info = M('users')
			->field('avatar,user_nicename,user_login')
			->where(array('id'=>$userid))
			->find();
		$info['avatar'] = "http://".$_SERVER['HTTP_HOST'].$info['avatar'];
		$this->assign('info',$info);
		$this->display('Mine/editMyMessagePhone');
	}
	
	//编辑收货地址（手机端）
	public function editAddressPhone()
	{
		$userid = $_SESSION['user']['id'];
		if (empty($userid)) {
			$this->ajaxReturn(array('code'=>400,'msg'=>'没有登录！'));exit();
		}
		if(IS_POST){	
			$sh_name = I('sh_name','');	
			$sh_mobile = I('sh_mobile','');	
			$sheng = I('sheng','');	
			$shi = I('shi','');	
			$qu = I('qu','');	
			$xiangxi = I('xiangxi','');	
			$status = I('status','');	
			$id = I('id','');//地址id
			if(empty($sh_name)){
				$this->ajaxReturn(array('code'=>400,'msg'=>'请填写收货人姓名！'));exit();
			}
			if(empty($sh_mobile)){
				$this->ajaxReturn(array('code'=>400,'msg'=>'请填写收货人手机号！'));exit();
			}
			if(empty($sheng) || empty($shi) || empty($qu) || empty($xiangxi)){
				$this->ajaxReturn(array('code'=>400,'msg'=>'请将收货地址填写完整！'));exit();
			}
			if(empty($id)){
				$this->ajaxReturn(array('code'=>400,'msg'=>'地址id不能为空！'));exit();
			}
			$data1 = array(
				'sh_name' => $sh_name,
				'sh_mobile' => $sh_mobile,
				'sheng' => $sheng,
				'shi' => $shi,
				'qu' => $qu,
				'xiangxi' => $xiangxi,
			);
			$res1 = M('user_address')->where(array('id'=>$id))->data($data1)->save();
			if($status == 1){
				$data2 = array(
					'sh_name' => $sh_name,
					'sh_mobile' => $sh_mobile,
					'sheng' => $sheng,
					'shi' => $shi,
					'qu' => $qu,
					'xiangxi' => $xiangxi,
					'address_id' => $id,
				);
				$res2 = M('users')->where(array('id'=>$userid))->data($data2)->save();
				if(!$res1){
					$this->ajaxReturn(array('code'=>400,'msg'=>'设置默认地址成功！'));exit();
				}
				if(!$res2){
					$this->ajaxReturn(array('code'=>400,'msg'=>'设置为默认地址失败！'));exit();
				}
				$this->ajaxReturn(array('code'=>200,'msg'=>'操作成功！'));exit();
			}else{
				if(!$res1){
					$this->ajaxReturn(array('code'=>400,'msg'=>'修改失败！'));exit();
				}
				$this->ajaxReturn(array('code'=>200,'msg'=>'操作成功！'));exit();
			}
		
		}else{
			$id = I('id','');
			if(empty($id)){
				$this->error('收货地址id为空！');
			}
			$info = M('user_address')->where(array('id'=>$id))->find();
			$mraddress_id = M('users')->where("id=$userid")->getField('address_id');
			if($mraddress_id == $id){
				$info['status'] = 1;
			}else{
				$info['status'] = 0;
			}
			$this->assign('info',$info);
//			dump($info);
			$this->display('Mine/editAddressPhone');
		}
	}


}
