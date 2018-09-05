<?php
namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
 * 微信支付
 */
class WxpayController extends HomebaseController
{
    public function _initialize(){
        parent::_initialize();
        require_once VENDOR_PATH."WxpayAPI/lib/WxPay.Api.php";
        require_once VENDOR_PATH."WxpayAPI/example/WxPay.NativePay.php";
        require_once VENDOR_PATH.'WxpayAPI/example/log.php';
    }

    /**
     * 提交完订单后选择支付方式页面
     */
    public function wxpay(){
        $order_sn = I('order_sn');
        $order_status = M('order')->where(array('order_sn'=>$order_sn))->getField('status');
        if($order_status !=1){
            $this->ajaxReturn(array('code'=>400,'msg'=>'该订单已被支付！'));exit();
        }
        $total_price = I('total_price')*100;
        $title = I('title');
        $notify_url = "http://".$_SERVER['HTTP_HOST']."/Wxpay/notify";;
        $notify = new \NativePay();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($title);
        $input->SetAttach("附加数据");
        $input->SetOut_trade_no($order_sn);
        $input->SetTotal_fee($total_price);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($title);
        $input->SetNotify_url($notify_url);
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($order_sn);
        $result = $notify->GetPayUrl($input);
        $url2 = $result["code_url"];
        $data = "<img alt='模式二扫码支付' src='http://paysdk.weixin.qq.com/example/qrcode.php?data=".urlencode($url2)."' style='width:150px;height:150px;'/>";
        $this->ajaxReturn(array('code'=>200,'msg'=>'生成二维码成功！','data'=>$data));
        // $this->display();
    }

    /**
     * 微信支付成功回调
     */
    public function notify(){
        $xml = $GLOBALS['HTTP_RAW_POST_DATA']; //返回的xml
        // file_put_contents(dirname(__FILE__).'/xml.txt',$xml); 
        $xmlObj=simplexml_load_string($xml,'SimplexmlElement',LIBXML_NOCDATA); 
        $xmlArr=json_decode(json_encode($xmlObj),true);
        $out_trade_no=$xmlArr['out_trade_no']; //订单号
        $result_code=$xmlArr['result_code']; //状态
        if($result_code=='SUCCESS'){ //数据库操作
            $paytime = time();
            $data = array(
                'status' => 2,
                'paytime' => $paytime,
            );
            M('order')->data($data)->where("order_sn=$out_trade_no")->save();
            echo 'SUCCESS'; 
            exit;   
        }else{ //失败
            return;
            exit;
        }

    }
}