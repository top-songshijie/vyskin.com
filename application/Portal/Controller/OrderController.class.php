<?php
namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
* 订单
*/
class OrderController extends HomebaseController
{
    //订单确认
    public function orderConfirmation()
    {
        //購物車id
        $id = I('id','');
//        dump($id);exit();
        if(empty($id)){
            $this->error('沒有選擇任何商品！');exit();
        }
        $my_address = $this->myAddress();
		$user_address = $this->myAllAddress();
        $list['data'] = M('cart as c')
            ->field('g.title,g.smeta,c.price,c.number,c.attribute,c.id')
            ->join('cmf_goods as g on c.goods_id=g.id')
            ->where("c.id in ($id)")
            ->select();
        $total_price = 0;
        foreach ($list['data'] as $key => $value) {
            $list['data'][$key]['smeta'] = json_decode($value['smeta'])->thumb;
            $list['data'][$key]['attribute'] = json_decode($value['attribute']);
            $list['data'][$key]['small_price'] = $value['price']*$value['number'];
            $total_price+=$value['price']*$value['number'];
       }
        $list['total_price'] = $total_price;
        $this->assign('list',$list);
		$this->assign('id',$id);//购物车id，为返回刷新参数
		$this->assign('my_address',$my_address);
        $this->assign('user_address',$user_address);
        $this->display('Order/orderConfirmation');
    }

    //订单确认(手机直接购买)
    public function orderConfirmationphone()
    {
        $my_address = $this->myAddress();

        if($_SESSION['datazhijiecar']){
            $goods_id = $_SESSION['datazhijiecar']['goods_id'];
            $number = $_SESSION['datazhijiecar']['number'];
            $select = $_SESSION['datazhijiecar']['select'];
        }else{
            $goods_id = I('goods_id','');
            $number = I('number','');
            $select = I('select','');
            if(empty($goods_id) || empty($number) || empty($select)){
                $this->error('缺少参数!');
            }
            $datazhijiecar = array(
                'goods_id'=>$goods_id,
                'number'=>$number,
                'select'=>$select,
            );

            session('datazhijiecar',$datazhijiecar);

            if(empty($_SESSION['datazhijiecar'])){
                $this->error('保存到session未成功!');
            }
        }

        $info = M('goods')
            ->field('title,smeta')
            ->where(array('id'=>$goods_id))
            ->find();
        $info['smeta'] = sp_get_image_preview_url(json_decode($info['smeta'])->thumb);
        $info['number'] = $number;
        $select = explode(",", $select);
        $info['guige'] = $select[0];
        $info['attribute'] = $select[1];
        $info['price'] = $select[2];
        $info['goods_id'] = $goods_id;
        $info['total_price'] = $info['price']*$number;
//        dump($info);
        $this->assign('my_address',$my_address);
        $this->assign('info',$info);
        $this->display('Order/orderConfirmationphone');
    }
	
	//订单确认(PC直接购买)
    public function orderConfirmationpc()
    {
		
        $user_address = $this->myAllAddress();
        if($_SESSION['datazhijiecar']){
            $goods_id = $_SESSION['datazhijiecar']['goods_id'];
            $number = $_SESSION['datazhijiecar']['number'];
            $select = $_SESSION['datazhijiecar']['select'];
        }else{
            $goods_id = I('goods_id','');
            $number = I('number','');
            $select = I('select','');
            if(empty($goods_id) || empty($number) || empty($select)){
                $this->error('缺少参数!');
            }
            $datazhijiecar = array(
                'goods_id'=>$goods_id,
                'number'=>$number,
                'select'=>$select,
            );

            session('datazhijiecar',$datazhijiecar);

            if(empty($_SESSION['datazhijiecar'])){
                $this->error('保存到session未成功!');
            }
        }

        $info = M('goods')
            ->field('title,smeta')
            ->where(array('id'=>$goods_id))
            ->find();
        $info['smeta'] = sp_get_image_preview_url(json_decode($info['smeta'])->thumb);
        $info['number'] = $number;
        $select = explode(",", $select);
        $info['guige'] = $select[0];
        $info['attribute'] = $select[1];
        $info['price'] = $select[2];
        $info['goods_id'] = $goods_id;
        $info['total_price'] = $info['price']*$number;
//        dump($info);
        $this->assign('user_address',$user_address);
        $this->assign('info',$info);
        $this->display('orderConfirmationpc');
    }

//    //成功提交页面(备份)
//    public function sucPaybak()
//    {
//        $order_sn = I('order_sn','');//订单编号
//        $id = I('id');//订单id
//        if(empty($order_sn) && empty($id)){
//            $this->error('缺少订单id，订单编号中的任何一个');
//        }
//        if(empty($id)){
//            $id = M('order')->where(array('order_sn'=>$order_sn))->getField('id');
//        }
//        $info = M('order as o')
//            ->field('o.order_sn,o.price as total_price,o.sh_name,o.sh_mobile,o.sh_address,o.addtime,od.good_id,od.attribute,g.title')
//            ->join('cmf_order_detail as od on o.order_sn=od.order_sn')
//            ->join("cmf_goods as g on g.id=od.good_id")
//            ->where(array('o.id'=>$id))->find();
//
//        $this->assign('info',$info);
////        dump($info);
//        $this->display();
//    }

    //成功提交页面
    public function sucPay()
    {
        $order_sn = I('order_sn','');//订单编号
//		dump($order_sn);exit();
        $id = I('id','');//订单id
        if(empty($order_sn) && empty($id)){
            $this->error('缺少订单id，订单编号中的任何一个');
        }
		if(empty($id)){
			$id = M('order')->where(array('order_sn'=>$order_sn))->getField('id');
		}
        if(empty($order_sn)){
            $order_sn = M('order')->where(array('id'=>$id))->getField('order_sn');
        }
        $list = M('order_detail as od')
            ->field('od.good_id,od.attribute,g.title,g.smeta,od.price as one_price,od.number')
            ->join('cmf_goods as g on od.good_id=g.id')
            ->where(array('od.order_sn'=>$order_sn))
            ->select();
        foreach ($list as $k=>$v){
            $list[$k]['attribute'] = json_decode($v['attribute']);
            $list[$k]['smeta'] = sp_get_image_preview_url(json_decode($v['smeta'])->thumb);
        }
        $info = M('order')
            ->field('order_sn,price as total_price,sh_name,sh_mobile,sh_address,addtime')
            ->where(array('id'=>$id))->find();
        $info['detail'] = $list;
        $this->assign('info',$info);
//        dump($info);
        $this->display('Order/sucPay');
    }

    //我的默认地址
    private function myAddress()
    {
        $userid = $_SESSION['user']['id'];
        if(empty($userid)){
			$this->redirect('User/Login/login');exit();
//            $this->error('您还没有登录！',U('User/Login/login'));exit();
        }
        $user_address = M('users')->field('sh_name,sh_mobile,sheng,shi,qu,xiangxi')->where(array('id'=>$userid))->find();
        return $user_address;
    }
	
	//我的所有地址
    private function myAllAddress()
    {
        $userid = $_SESSION['user']['id'];
        if(empty($userid)){
			$this->redirect('User/Login/login');exit();
//            $this->error('您还没有登录！');exit();
        }
        $user_address = M('user_address')->field('id,sh_name,sh_mobile,sheng,shi,qu,xiangxi')->where(array('userid'=>$userid))->select();
        return $user_address;
    }

    /**
     *查询每种状态订单的个数
     */
    public function get_everyorder_num()
    {
        $userid = $_SESSION['user']['id'];
        if(empty($userid)){
            $this->ajaxReturn(array('status'=>0,'info'=>'没有登录'));
            exit();
        }
        $order_model = M('order');
        //未付款
        $order1=$order_model->where(array('user_id'=>$userid,'status'=>1))->count();
        //已付款
        $order2=$order_model->where(array('user_id'=>$userid,'status'=>2))->count();
        //已完成
        $order3=$order_model->where(array('user_id'=>$userid,'status'=>3))->count();
        //已取消
        $order4=$order_model->where(array('user_id'=>$userid,'status'=>4))->count();
        $data = array('未支付'=>$order1,'已支付'=>$order2,'已完成'=>$order3,'已取消'=>$order4);
        // dump($data);exit();
        $this->ajaxReturn($data);
    }


    /**
     * 我的订单单个订单查询
     * @return [type] [description]
     */
    public function order_detail()
    {
        $userid = $_SESSION['user']['id'];
        if(empty($userid)){
            $this->ajaxReturn(array('status'=>0,'info'=>'没有登录'));
            exit();
        }
        //查询一个订单
        $order_sn = I('order_sn','');
        if(empty($order_sn)){
            $this->ajaxReturn(array('status'=>0,'info'=>'没有订单号'));
            exit();
        }
        $where['order_sn'] = $order_sn;
        //查询订单表
        $order=M('order')
        	->field('order_sn,status,addtime,price,sh_name,sh_mobile,sh_address')
            ->where($where)
            ->find();
        //调整时间格式
        $order['addtime'] = date("Y-m-d",$order['addtime']);
        //查询详情
        $order_detail=M('order_detail as od')
            ->field('g.smeta,g.title,od.attribute,od.number,od.price')
            ->join('cmf_goods as g on od.good_id=g.id')
            ->where(array('od.order_sn'=>$order['order_sn']))
            ->select();
        foreach ($order_detail as $key => $value) {
        	$order_detail[$key]['smeta'] = json_decode($order_detail[$key]['smeta']);
        	$order_detail[$key]['smeta'] = $order_detail[$key]['smeta']->thumb;
        	$order_detail[$key]['attribute'] = json_decode($order_detail[$key]['attribute']);
        	$order_detail[$key]['attribute'] = $order_detail[$key]['attribute'][1];

        }
        $order['detail']=$order_detail;
       // dump($order);exit();
       $this->ajaxReturn($order);
    }



    /**
     * 取消订单
     */
    public function cencelOrder()
    {
        $order_sn = I('order_sn','');
        if(empty($order_sn)) {
            $this->error('缺少订单编号');exit();
        }
        $res = M('order')->data(array('status'=>'4'))->where("order_sn=$order_sn")->save();
        if($res) {
            $this->success('已取消',"Mine/index");
        }else{
            $this->error('出现错误');exit();
        }
    }

	 /**
     * 删除订单
     */
    public function delOrder()
    {
        $order_sn = I('order_sn','');
        if(empty($order_sn)) {
            $this->error('缺少订单编号');exit();
        }
        $res = M('order')->where("order_sn=$order_sn")->delete();
        if($res) {
            $this->success('删除订单成功！');
        }else{
            $this->error('出现错误！');exit();
        }
    }

    /**
     * 从购物车添加到订单
     */
    public function addorderfromcart()
    {
        //用户id
        $userid = $_SESSION['user']['id'];
        $cart_id = I('cart_id','');
        $address_id = I('address_id','');
        if(empty($address_id)){
            $this->ajaxReturn(array('code'=>400,'msg'=>'請選擇收貨地址！'));exit();
        }
        if(empty($userid) || empty($cart_id)){
            $this->ajaxReturn(array('code'=>400,'msg'=>'缺少必要参数！'));exit();
        }
		
        //订单号
        $order_sn = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        //添加时间
        $addtime = time();
        $user_address=M('user_address')->field('sh_name,sh_mobile,sheng,shi,qu,xiangxi')->where("id=$address_id")->find();
        if(empty($user_address['sh_name']) || empty($user_address['sh_mobile']) || empty($user_address['sheng']) || empty($user_address['shi']) || empty($user_address['qu']) || empty($user_address['xiangxi'])){
            $this->ajaxReturn(array('code'=>400,'msg'=>'收貨地址不完整！'));exit();
        }
        //收货人
        $sh_name = $user_address['sh_name'];
        //收货电话
        $sh_mobile = $user_address['sh_mobile'];
        //收货地址
        $sh_address = $user_address['sheng'].$user_address['shi'].$user_address['qu'].$user_address['xiangxi'];
        //购物车id
        $id=$cart_id;
        //添加到订单
        $data_order['user_id'] = $userid;
        $data_order['order_sn'] = $order_sn;
        $data_order['addtime'] = $addtime;
        $data_order['sh_name'] = $sh_name;
        $data_order['sh_mobile'] = $sh_mobile;
        $data_order['sh_address'] = $sh_address;
		//添加到订单详情
        $price = 0;
        foreach ($id as $key => $value) {
            $cart = M('cart')->where("id=$value")->find();
            $data_order_detail['order_sn'] = $order_sn;
            //商品id
            $data_order_detail['good_id'] = $cart['goods_id'];
            //商品单价
            $data_order_detail['price'] = $cart['price'];
            //数量
            $data_order_detail['number'] = $cart['number'];
            //小计金额
            $data_order_detail['amount'] = $cart['price']*$cart['number'];
            //总金额
            $price+=$data_order_detail['amount'];
            //商品属性
            $data_order_detail['attribute'] = $cart['attribute'];
            $res_order_detail = M('order_detail')->data($data_order_detail)->add();
            //删除购物车
            $res_del=M('cart')->where(array('id'=>$cart['id']))->delete();
        }
        $data_order['price'] = $price;
        $res_order = M('order')->data($data_order)->add();
        if($res_order && $res_order_detail && $res_del) {
            $this->ajaxReturn(array('code'=>200,'msg'=>'操作成功','data'=>$res_order));
        }else{
            $this->ajaxReturn(array('code'=>400,'msg'=>'出现错误'));
        }
    }
	
	 /**
     * 加订单直接购买（手机端）
     */
    public function addOrderPc()
    {
        $userid = $_SESSION['user']['id'];//用户id
        if(empty($userid)){
            $this->ajaxReturn(array('code'=>400,'msg'=>'没有登录'));exit();
        }        
        $good_id = I('post.goods_id','');//商品id
		$address_id = I('address_id','');
		$order_sn = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);//订单号        
		$addtime = time();//添加时间
   
		$user_address=M('user_address')->field('sh_name,sh_mobile,sheng,shi,qu,xiangxi')->where(array('id'=>$address_id))->find();
        
		if(empty($user_address['sh_name']) || empty($user_address['sh_mobile']) || empty($user_address['sheng']) || empty($user_address['shi']) || empty($user_address['qu']) || empty($user_address['xiangxi'])){
            $this->ajaxReturn(array('code'=>0,'msg'=>'请选择收货地址！'));exit();
        } 

        $sh_name = $user_address['sh_name'];//收货人      
        $sh_mobile = $user_address['sh_mobile'];//收货电话   
        $sh_address = $user_address['sheng'].$user_address['shi'].$user_address['qu'].$user_address['xiangxi'];//收货地址
        //属性及单价
        $price = I('post.price');
        $guige = I('post.guige');
        $attribute = I('post.attribute');
        $arr_select = array($guige,$attribute);
        $arr_json = json_encode($arr_select,true);//商品属性json格式   
        $number = I('post.number');//商品数量
		//添加到订单
        $data_order['user_id'] = $userid;
        $data_order['order_sn'] = $order_sn;
        $data_order['addtime'] = $addtime;
        $data_order['sh_name'] = $sh_name;
        $data_order['sh_mobile'] = $sh_mobile;
        $data_order['sh_address'] = $sh_address;
        $data_order['price'] = $price*$number;
        //添加操作
        $res_order = M('order')->data($data_order)->add();
        //添加到订单详情
        $data_order_detail['order_sn'] = $order_sn; //订单号        
        $data_order_detail['good_id'] = $good_id;//商品id        
        $data_order_detail['price'] = $price;//商品单价        
        $data_order_detail['number'] = $number;//数量       
        $data_order_detail['amount'] = $price*$number;//小计金额       
        $data_order_detail['attribute'] = $arr_json;//商品属性
        //添加操作
        $res_order_detail = M('order_detail')->data($data_order_detail)->add();
        if($res_order && $res_order_detail) {
            $this->ajaxReturn(array('code'=>200,'msg'=>'操作成功','data'=>$res_order));
        }else{
            $this->ajaxReturn(array('code'=>400,'msg'=>'出现错误'));
        }
    }

    /**
     * 从购物车添加到订单(phone)
     */
    public function addorderfromcartphone()
    {
        //用户id
        $userid = $_SESSION['user']['id'];
        $cart_id = I('cart_id','');

        if(empty($userid) || empty($cart_id)){
            $this->ajaxReturn(array('code'=>400,'msg'=>'缺少必要参数！'));exit();
        }
        //订单号
        $order_sn = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        //添加时间
        $addtime = time();
        $user_address=M('users')->field('sh_name,sh_mobile,sheng,shi,qu,xiangxi')->where("id=$userid")->find();
        if(empty($user_address['sh_name']) || empty($user_address['sh_mobile']) || empty($user_address['sheng']) || empty($user_address['shi']) || empty($user_address['qu']) || empty($user_address['xiangxi'])){
            $this->ajaxReturn(array('code'=>400,'msg'=>'收貨地址不完整！'));exit();
        }
        //收货人
        $sh_name = $user_address['sh_name'];
        //收货电话
        $sh_mobile = $user_address['sh_mobile'];
        //收货地址
        $sh_address = $user_address['sheng'].$user_address['shi'].$user_address['qu'].$user_address['xiangxi'];
        //购物车id
        $id=$cart_id;
        //添加到订单
        $data_order['user_id'] = $userid;
        $data_order['order_sn'] = $order_sn;
        $data_order['addtime'] = $addtime;
        $data_order['sh_name'] = $sh_name;
        $data_order['sh_mobile'] = $sh_mobile;
        $data_order['sh_address'] = $sh_address;
        //添加到订单详情
        $price = 0;
        foreach ($id as $key => $value) {
            $cart = M('cart')->where("id=$value")->find();
            $data_order_detail['order_sn'] = $order_sn;
            //商品id
            $data_order_detail['good_id'] = $cart['goods_id'];
            //商品单价
            $data_order_detail['price'] = $cart['price'];
            //数量
            $data_order_detail['number'] = $cart['number'];
            //小计金额
            $data_order_detail['amount'] = $cart['price']*$cart['number'];
            //总金额
            $price+=$data_order_detail['amount'];
            //商品属性
            $data_order_detail['attribute'] = $cart['attribute'];
            $res_order_detail = M('order_detail')->data($data_order_detail)->add();
            //删除购物车
            $res_del=M('cart')->where(array('id'=>$cart['id']))->delete();
        }
        $data_order['price'] = $price;
        $res_order = M('order')->data($data_order)->add();
        if($res_order && $res_order_detail && $res_del) {
            $this->ajaxReturn(array('code'=>200,'msg'=>'操作成功','data'=>$res_order));
        }else{
            $this->ajaxReturn(array('code'=>400,'msg'=>'出现错误'));
        }
    }

    /**
     * 加订单(直接购买)
     */
    public function addOrder()
    {
        $userid = $_SESSION['user']['id'];//用户id
        if(empty($userid)){
            $this->ajaxReturn(array('code'=>400,'msg'=>'没有登录'));exit();
        }        
        $good_id = I('post.goods_id');//商品id       
        $order_sn = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);//订单号        
        $addtime = time();//添加时间
        $user_address=M('users')->field('sh_name,sh_mobile,sheng,shi,qu,xiangxi')->where("id=$userid")->find();
        if(empty($user_address['sh_name']) || empty($user_address['sh_mobile']) || empty($user_address['sheng']) || empty($user_address['shi']) || empty($user_address['qu']) || empty($user_address['xiangxi'])){
            $this->ajaxReturn(array('code'=>0,'msg'=>'没有设置默认收货地址'));exit();
        }     
        $sh_name = $user_address['sh_name'];//收货人      
        $sh_mobile = $user_address['sh_mobile'];//收货电话   
        $sh_address = $user_address['sheng'].$user_address['shi'].$user_address['qu'].$user_address['xiangxi'];//收货地址
        //属性及单价
        $price = I('post.price');
        $guige = I('post.guige');
        $attribute = I('post.attribute');
        $arr_select = array($guige,$attribute);
        $arr_json = json_encode($arr_select,true);//商品属性json格式   
        $number = I('post.number');//商品数量
        //添加到订单
        $data_order['user_id'] = $userid;
        $data_order['order_sn'] = $order_sn;
        $data_order['addtime'] = $addtime;
        $data_order['sh_name'] = $sh_name;
        $data_order['sh_mobile'] = $sh_mobile;
        $data_order['sh_address'] = $sh_address;
        $data_order['price'] = $price*$number;
        //添加操作
        $res_order = M('order')->data($data_order)->add();
        //添加到订单详情
        $data_order_detail['order_sn'] = $order_sn; //订单号        
        $data_order_detail['good_id'] = $good_id;//商品id        
        $data_order_detail['price'] = $price;//商品单价        
        $data_order_detail['number'] = $number;//数量       
        $data_order_detail['amount'] = $price*$number;//小计金额       
        $data_order_detail['attribute'] = $arr_json;//商品属性
        //添加操作
        $res_order_detail = M('order_detail')->data($data_order_detail)->add();
        if($res_order && $res_order_detail) {
            $this->ajaxReturn(array('code'=>200,'msg'=>'操作成功','data'=>$res_order));
        }else{
            $this->ajaxReturn(array('code'=>400,'msg'=>'出现错误'));
        }

    }



    //检测是否付款成功
    public function jiance(){
        $order_sn = I('order_sn','');
        if(empty($order_sn)){
            $this->ajaxReturn(array('code'=>400,'msg'=>'订单编号不存在'));exit();
        }
        $status = M('order')->where(array('order_sn'=>$order_sn))->getField('status');
        if($status == 1){
            $this->ajaxReturn(array('code'=>400,'msg'=>'还未支付哦！'));exit();
        }else{
            $this->ajaxReturn(array('code'=>200,'msg'=>'支付成功！'));
        }
    }
}
