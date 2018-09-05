<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class  OrderController extends AdminbaseController{

    protected $order_model;
    protected $order_made;
    protected $order_service;

    public function _initialize() {
        parent::_initialize();
        $this->order_model = M("order");
        $this->order_made = M("order_made");
        $this->order_service = M("order_service");
    }

    //添加定制购买订单
    public function add(){
        $id = intval($_GET['id']);
        if(!$id){
            $this->error("非法操作！");
        }else{
            $this->assign("id",$id);
            $this->display();
        }
    }

    //获取所有商品
    public function allgoods(){
        if(IS_AJAX){
            $goods=M('goods')->field('id, title')->select();
            if($goods){
                $this->ajaxReturn(array('status' => true, 'data' => json_encode($goods)));
            }else{
                $this->ajaxReturn(array('status' => false, 'data' => '商品获取失败，请重新尝试'));
            }
        }else{
            $this->error("非法操作！");
        }
    }

    //添加定制购买订单
    public function add_order(){
        $id = intval($_GET['id']);
        if(!$id){
            $this->error("非法操作！");
        }else{
            require_once VENDOR_PATH."jssdk/jssdk.php";
            $jssdk = new \JSSDK(C('WX_APPID'), C('WX_APP_SECRET'));
            $access_token = $jssdk->getAccessToken();
            $where['id'] = $id;
            $made=M('order_made')
                ->field('id, user_id')
                ->where($where)
                ->find();
            $user = M('users')->field('openid, sh_name, sh_mobile, sh_address')
                ->where(array('id'=>$made['user_id']))
                ->find();
            foreach ($_POST as $v){
                $model = M('order');
                $model->startTrans();
                $order_sn = sp_get_order_sn();
                $data['order_sn'] = $order_sn;
                $data['user_id'] = $made['user_id'];
                $data['addtime'] = time();
                $data['type'] = 1;
                $data['status'] = 1;
                $data['sh_name'] = $user['sh_name'];
                $data['sh_mobile'] = $user['sh_mobile'];
                $data['sh_address'] = $user['sh_address'];
                $data['number']=array_sum($v['number']);
                $data['price']=array_sum($v['price']);
                $data['is_made']=1;
                $row=$model->add($data);
                $amount=0;
                $i=0;
                $goods_id = implode(",", $v['goods_id']);
                $goods_list = M('goods')->field('title')
                    ->where(array('id'=>array('IN',$goods_id)))
                    ->select();
                $goods_name = "";
                foreach ($goods_list as $kn=>$vn){
                    $goods_name .= $vn['title'].",";
                }
                $goods_name = substr($goods_name,0,strlen($goods_name)-1);
                foreach ($v['goods_id'] as $kg=>$vg) {
                    $detail['order_sn'] = $order_sn;
                    $detail['good_id'] = $vg;
                    $detail['number'] = $v['number'][$kg];
                    $detail['price'] = $v['price'][$kg];
                    $detail['amount'] = $v['price'][$kg] * $v['number'][$kg];
                    $detail['attribute'] = $v['attribute'][$kg];
                    $amount += $detail['amount'];
                    $row2 = M('OrderDetail')->add($detail);
                    if (!$row2) {
                        $i++;
                    }
                }
                $send = $this->sendTemplets($access_token, $user['openid'], $goods_name, $order_sn);
                if($i==0 && $send){
                    $model->commit();
                    $this->success('订单添加成功');
                }else{
                    $model->rollback();
                    $this->error('订单添加失败');
                }
            }
        }
    }

    //发送模板消息
    private function sendTemplets($access_token, $openid, $goods_name, $order_sn){
        $data = array();
        $data['touser'] = $openid;
        $data['template_id'] = 'gNcJBqBD1qsIwzXVQ9H2CfqpsfkgncLtt1Ux-loNI58';
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $data['url'] = $protocol.$_SERVER[HTTP_HOST]."/index.php/User/Index/order_detail/order_sn/".$order_sn;
        $data['data']['first']['value'] = '尊敬的用户，您好';
        $data['data']['first']['color'] = '#06C';
        $data['data']['Content1']['value'] = '您的预约订单进度如下';
        $data['data']['Content1']['color'] = '#06C';
        $data['data']['Good']['value'] = $goods_name;
        $data['data']['Good']['color'] = '#06C';
        $data['data']['contentType']['value'] = '订单待支付';
        $data['data']['contentType']['color'] = '#06C';
        $data['data']['remark']['value'] = "点击查看详情";
        $data['data']['remark']['color'] = '#06C';
        $data = json_encode($data);

        $data=preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $data);
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token."";

        if($this->post_by_curl($url,$data)){
            return true;
        }
    }

    function post_by_curl($url,$data)
    {
        $curl = curl_init();// 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址

        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_POST, true); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS,  $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 500); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, false); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // 获取的信息以文件流的形式返回
        $result = curl_exec($curl); // 执行操作
        if (curl_errno($curl)){
            echo 'Error POST'.curl_error($curl);
        }
        if($result) {
            return $result;
        }
    }
    //商品订单
    public function goods(){
        $keyword=I('request.keyword');
        if(!empty($keyword)){
            $keyword_complex=array();
            $keyword_complex['order_sn']  = array('like',"%$keyword%");
            $keyword_complex['user_login']  = array('like',"%$keyword%");
            $keyword_complex['_logic'] = 'or';
            $where['_complex'] = $keyword_complex;
        }
        $start_time=strtotime(I('request.start_time'));
        if(!empty($start_time)){
            $where['addtime']=array(
                array('EGT',$start_time)
            );
        }
        $end_time=strtotime(I('request.end_time'));
        if(!empty($end_time)){
            if(empty($where['addtime'])){
                $where['addtime']=array();
            }
            array_push($where['addtime'], array('ELT',$end_time));
        }
        $status=I('request.status');
        if (!empty($status)){
            if ($status!='all'){
                $where['status']=$status;
            }
        }else{
//            $where['status']=2;
//            $_POST['status']=2;
        }

        $count=$this->order_model->alias('o')
            ->join('cmf_users as u on u.id=o.user_id','left')
            ->where($where)
            ->count('o.id');
        $page = $this->page($count, 20);

        $order=$this->order_model->alias('o')
            ->join('cmf_users as u on u.id=o.user_id','left')
            ->field('o.*,user_login')
            ->where($where)
            ->order('addtime desc')
            ->limit($page->firstRow , $page->listRows)
            ->select();
//        $order=$this->order_model->select();
//        dump($order);
        $this->assign("page", $page->show('Admin'));
        $this->assign("formget",array_merge($_GET,$_POST));
        $this->assign("order",$order);
        $this->display();
    }

    //商品订单详情
    public function detail2(){
        $id=intval($_GET['id']);
        $order=$this->order_model->alias('o')
            ->join('cmf_users as u on u.id=o.user_id','left')
            ->field('o.*,user_nicename,u.user_login')
            ->where("o.id=$id")
            ->order('addtime desc')
            ->find();
        $detail=M('order_detail')->alias('o')
            ->join('cmf_goods as g on g.id=o.good_id','left')
            ->field('o.price,o.number,amount,o.attribute,title,good_id,smeta')
            ->where(array('order_sn'=>$order['order_sn']))
            ->select();
        $amount=M('order_detail')
            ->where(array('order_sn'=>$order['order_sn']))
            ->sum('amount');
        $this->assign("id",$id);
        $this->assign("detail",$detail);
        $this->assign("order",$order);
        $this->assign("amount",$amount);
        $this->display();
    }

    //折扣设置
    public function discount(){
        $discount=M('discount')
            ->find();
        $this->assign("discount",$discount);
        $this->assign("id",$discount['id']);
        $this->display();
    }

    //折扣设置修改
    public function change_discount(){
        if(IS_POST) {
            $id = intval($_GET['id']);
            $data['discount']=$_POST['discount'];
            $row=M('discount')->where(array('id'=>$id))->save($data);
            if($row){
                $this->success('折扣比例修改成功');
            }else{
                $this->error('折扣比例修改失败');
            }
        }
    }

    //修改订单价格或单号
    public function change_price(){
        if(IS_POST) {
            $id = intval($_GET['id']);
            $data['price']=$_POST['price'];
            if($data['price']>0){
                $row=M('order')->where(array('id'=>$id))->save($data);
                if($row){
                    $this->success('订单价格修改成功');
                }else{
                    $this->error('订单价格修改失败');
                }
            }
            $datas['shipment']=$_POST['shipment'];
            if($datas['shipment']){
                $row=M('order')->where(array('id'=>$id))->save($datas);
                if($row){
                    $this->success('发货单号修改成功');
                }else{
                    $this->error('发货单号修改失败');
                }
            }
        }
    }

    //发货
//    public function fahuo(){
//        if(IS_POST){
//            $id=intval($_POST['id']);
//            $data['status']=5;
//            $data['shipment']=$_POST['shipment'];
//            $row=M('order')->where(array('id'=>$id))->save($data);
//            if($row){
//                $this->success('已发货');
//            }else{
//                $this->error('发货失败');
//            }
//        }else{
//            $id=intval($_GET['id']);
//            $order=$this->order_model->alias('o')
//                ->join('cmf_users as u on u.id=o.user_id','left')
//                ->field('o.*,user_nicename,u.mobile')
//                ->where("o.id=$id")
//                ->order('addtime desc')
//                ->find();
//            $detail=M('order_detail')->alias('o')
//                ->join('cmf_goods as g on g.id=o.good_id','left')
//                ->field('o.price,o.number,amount,o.attribute,title,good_id,smeta')
//                ->where(array('order_sn'=>$order['order_sn']))
//                ->select();
//            $amount=M('order_detail')
//                ->where(array('order_sn'=>$order['order_sn']))
//                ->sum('amount');
//            $this->assign("detail",$detail);
//            $this->assign("order",$order);
//            $this->assign("amount",$amount);
//            $this->display();
//        }
//    }
//
//    public function made(){
//        $this->_lists();
//        $this->display();
//    }
    public function fahuo() {
        $id = I('id');
        $res = M('order')->where("id=$id")->data(array('status'=>5))->save();
        if($res){
            $this->success("发货成功！", U("Order/goods"));
        }else{
            $this->error("发货失败！");
        }
    }

    //售后提交列表
    public function service(){
        $this->_listservice();
        $this->display();
    }

    //售后提交详情
    public function details(){
        $id=intval($_GET['id']);
        $service=$this->order_service->alias('s')
            ->join('cmf_order as o on o.order_sn=s.order_sn','left')
            ->join('cmf_users as u on o.user_id=u.id','left')
            ->field('s.order_sn, s.service, s.sort, s.content, s.thumb, s.ctime, o.id, o.user_id, o.sh_name, o.sh_mobile, o.sh_address, u.user_nicename')
            ->where("s.id=$id")
            ->find();
        $thumb = explode(",", $service['thumb']);
        //var_dump($service);
        $this->assign("id",$id);
        $this->assign("thumb",$thumb);
        $this->assign("service",$service);
        $this->display();
    }

    /**
     * 定制购买列表处理方法,根据不同条件显示不同的列表
     * @param array $where 查询条件
     */
    private function _listservice($where=array()){
        $order_service=$this->order_service->find();
        $this->assign("order_service",$order_service);

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
            $where['order_sn']=array('like',"%$keyword%");
            $where['service']=array('like',"%$keyword%");
            $where['sort']=array('like',"%$keyword%");
            $where['content']=array('like',"%$keyword%");
        }

        $count=$this->order_service->where($where)->count('id');

        $page = $this->page($count, 20);

        $posts=$this->order_service->field('*')
            ->where($where)
            ->limit($page->firstRow , $page->listRows)
            ->order("ctime DESC")->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign("formget",array_merge($_GET,$_POST));
        $this->assign("posts",$posts);
    }

    /**
     * 定制购买列表处理方法,根据不同条件显示不同的列表
     * @param array $where 查询条件
     */
    private function _lists($where=array()){
        $order_made=$this->order_made->find();
        $this->assign("order_made",$order_made);

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
            $where['goods_need']=array('like',"%$keyword%");
            $where['content']=array('like',"%$keyword%");
            $where['remark']=array('like',"%$keyword%");
        }

        $count=$this->order_made->where($where)->count('id');

        $page = $this->page($count, 20);

        $posts=$this->order_made->field('*')
            ->where($where)
            ->limit($page->firstRow , $page->listRows)
            ->order("ctime DESC")->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign("formget",array_merge($_GET,$_POST));
        $this->assign("posts",$posts);
    }

    /**
     * 完成订单
     */
    public function finishorder(){
        $id = I('id');
        $res = M('order')->where("id=$id")->data(array('status'=>3))->save();
        if($res){
            $this->success("修改成功！", U("Order/goods"));
        }else{
            $this->error("修改失败！");
        }
    }
}