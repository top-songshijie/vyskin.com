<?php
/**
 * Created by PhpStorm.
 * User: Tiger Yang
 * Date: 2017/8/3
 * Time: 14:36
 */

namespace Portal\Controller;
use Common\Controller\NoLimitController;

class PayController extends NoLimitController {

    public function _initialize(){
        parent::_initialize();
        require_once VENDOR_PATH."WxpayAPI/lib/WxPay.Api.php";
        require_once VENDOR_PATH."WxpayAPI/example/WxPay.JsApiPay.php";
        require_once VENDOR_PATH."WxpayAPI/example/WxPay.NativePay.php";
        require_once VENDOR_PATH."WxpayAPI/lib/WxPay.Notify.php";
        require_once VENDOR_PATH.'WxpayAPI/example/log.php';
    }

    public function index(){
        $notify = new NativePay();
        $input = new WxPayUnifiedOrder();
        $input->SetBody("test");
        $input->SetAttach("test");
        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee("1");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url("http://vyskin.com/Order/notify");
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id("123456789");
        $result = $notify->GetPayUrl($input);
        $url2 = $result["code_url"];
        $this->assign('url2',$url2);
        $this->display();
    }

    public function notify(){
        $notify = new \WxPayNotify();
        $notify->Handle(false);

        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];

        $base = new \WxPayResults();
        $data = $base->FromXml($xml);
        F('return_xml',$data);
        if($base->CheckSign() == true){
            if ($data["return_code"] == "FAIL") {
                $in['status'] = -1;
                M('orders')->where(array('id'=>$data['attach']))->save($in);
            }elseif($data["result_code"] == "FAIL"){
                $in['status'] = -2;
                M('orders')->where(array('id'=>$data['attach']))->save($in);
            }else{
                $in['status'] = 2;
                $in['addtime'] = date('Y-m-d H:i:s',time());
                $in['transaction_id'] = $data['transaction_id'];
                M('orders')->where(array('id'=>$data['attach']))->save($in);
                $money =   M('orders')->where(array('id'=>$data['attach']))->getField('all_money,freight');
                $uid= sp_get_current_userid();
                M('users')->where("id=$uid")->setInc('score',$money['freight']);
            }
        }
    }

}