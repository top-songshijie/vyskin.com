<?php
namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
 * 支付宝支付（包括pc和手机端）
 */
class AlipayController extends HomebaseController
{
    //手机端
    public function wapAlipay()
    {
        header("Content-type:text/html;charset=utf-8");
        $total_price = I('total_price','');
        $order_sn = I('order_sn','');
        $order_status = M('order')->where(array('order_sn'=>$order_sn))->getField('status');
        if($order_status != 1){
            $this->error('该订单已被支付');exit();
        }
        $title = I('title','');
        if(empty($total_price) || empty($order_sn) || empty($title)){
            $this->error('缺少参数！');exit();
        }
         require_once VENDOR_PATH."alipay.trade.wap.pay/wappay/service/AlipayTradeService.php";
         require_once VENDOR_PATH."alipay.trade.wap.pay/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php";
         require_once VENDOR_PATH."alipay.trade.wap.pay/config.php";  

	    //商户订单号，商户网站订单系统中唯一订单号，必填
	     $out_trade_no = $order_sn;
	    
	    //订单名称，必填
	     $subject = $title;

	    //付款金额，必填
	     $total_amount = $total_price;

	    //商品描述，可空
	    // $body = $_POST['WIDbody'];
	    $body = "";

	    //超时时间
	    $timeout_express="1m";

	    $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
	    $payRequestBuilder->setBody($body);
	    $payRequestBuilder->setSubject($subject);
	    $payRequestBuilder->setOutTradeNo($out_trade_no);
	    $payRequestBuilder->setTotalAmount($total_amount);
	    $payRequestBuilder->setTimeExpress($timeout_express);
		// var_dump($config);exit;
	    $payResponse = new \AlipayTradeService($config);
	    $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
	    return ;
    }

    //手机端同步
    function wap_return_url()
    {
        $this->success('支付成功，正在跳转！',U('Mine/index'));
    }

    //手机端异步
    function wap_notify_url()
    {
        //商户订单号		
		$out_trade_no = $_POST['out_trade_no'];
        $paytime = time();
        $data = array(
            'status' => 2,
            'paytime' => $paytime,
        );
		M('order')->data($data)->where("order_sn=$out_trade_no")->save();
    }

    //PC端
    public function pageAlipay()
    {
        header("Content-type:text/html;charset=utf-8");
        $total_price = I('total_price','');
        $order_sn = I('order_sn','');
        $order_status = M('order')->where(array('order_sn'=>$order_sn))->getField('status');
        if($order_status !=1){
            $this->error('该订单已被支付');exit();
        }
        $title = I('title','');
        if(empty($total_price) || empty($order_sn) || empty($title)){
            $this->error('缺少参数！');exit();
        }
		require_once VENDOR_PATH."alipay.trade.page.pay/config.php";
		require_once VENDOR_PATH."alipay.trade.page.pay/pagepay/service/AlipayTradeService.php";
		require_once VENDOR_PATH."alipay.trade.page.pay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php";

		//商户订单号，商户网站订单系统中唯一订单号，必填
		// $out_trade_no = trim($_POST['WIDout_trade_no']);
		$out_trade_no = $order_sn;

		//订单名称，必填
		// $subject = trim($_POST['WIDsubject']);
		$subject = $title;

		//付款金额，必填
		// $total_amount = trim($_POST['WIDtotal_amount']);
		$total_amount = $total_price;

		//商品描述，可空
		// $body = trim($_POST['WIDbody']);
		$body = "";

		//构造参数
		$payRequestBuilder = new \AlipayTradePagePayContentBuilder();
		$payRequestBuilder->setBody($body);
		$payRequestBuilder->setSubject($subject);
		$payRequestBuilder->setTotalAmount($total_amount);
		$payRequestBuilder->setOutTradeNo($out_trade_no);
//		 var_dump($payRequestBuilder);exit;
//		 var_dump($config);exit;
		$aop = new \AlipayTradeService($config);

		/**
		* pagePay 电脑网站支付请求
		* @param $builder 业务参数，使用buildmodel中的对象生成。
		* @param $return_url 同步跳转地址，公网可以访问
		* @param $notify_url 异步通知地址，公网可以访问
		* @return $response 支付宝返回的信息
		*/
		$response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

		//输出表单
		var_dump($response);
    }

    //PC端同步
    function page_return_url()
    {
        $this->success('支付成功，正在跳转！',U('Mine/index'));
    }

    //PC端异步
    function page_notify_url()
    {
		//商户订单号		
		$out_trade_no = $_POST['out_trade_no'];
        $out_trade_no = $_POST['out_trade_no'];
        $paytime = time();
        $data = array(
            'status' => 2,
            'paytime' => $paytime,
        );
		M('order')->data($data)->where("order_sn=$out_trade_no")->save();

    }
}