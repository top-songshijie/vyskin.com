<?php
/* *
 * 功能：支付宝手机网站支付接口接口调试入口页面
 * 版本：3.4
 * 修改日期：2016-03-08
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */
?>
<!DOCTYPE html>
<html>
	<head>
	<title>支付宝手机网站支付接口接口</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" id="viewport" content="width=device-width, initial-scale=1">
<style>
    *{
        margin:0;
        padding:0;
    }
    ul,ol{
        list-style:none;
    }
    body{
        font-family: "Helvetica Neue",Helvetica,Arial,"Lucida Grande",sans-serif;
    }
    .hidden{
        display:none;
    }
    .new-btn-login-sp{
        padding: 1px;
        display: inline-block;
        width: 75%;
    }
    .new-btn-login {
        background-color: #02aaf1;
        color: #FFFFFF;
        font-weight: bold;
        border: none;
        width: 40%;
        height: 30px;
        border-radius: 5px;
        font-size: 16px;
    }
    #main{
        width:100%;
        margin:0 auto;
        font-size:14px;
    }
    .red-star{
        color:#f00;
        width:10px;
        display:inline-block;
    }
    .null-star{
        color:#fff;
    }
    .content{
        margin-top:5px;
    }
    .content dt{
        width:100px;
        display:inline-block;
        float: left;
        margin-left: 20px;
        color: #666;
        font-size: 13px;
        margin-top: 8px;
    }
    .content dd{
        margin-left:120px;
        margin-bottom:5px;
    }
    .content dd input {
        width: 85%;
        height: 28px;
        border: 0;
        -webkit-border-radius: 0;
        -webkit-appearance: none;
    }
    #foot{
        margin-top:10px;
        position: absolute;
        bottom: 15px;
        width: 100%;
    }
    .foot-ul{
        width: 100%;
    }
    .foot-ul li {
        width: 100%;
        text-align:center;
        color: #666;
    }
    .note-help {
        color: #999999;
        font-size: 12px;
        line-height: 130%;
        margin-top: 5px;
        width: 100%;
        display: block;
    }
    #btn-dd{
        margin: 20px;
        text-align: center;
    }
    .foot-ul{
        width: 100%;
    }
    .one_line{
        display: block;
        height: 1px;
        border: 0;
        border-top: 1px solid #eeeeee;
        width: 100%;
        margin-left: 20px;
    }
    .am-header {
        display: -webkit-box;
        display: -ms-flexbox;
        display: box;
        width: 100%;
        position: relative;
        padding: 7px 0;
        -webkit-box-sizing: border-box;
        -ms-box-sizing: border-box;
        box-sizing: border-box;
        background: #1D222D;
        height: 50px;
        text-align: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        box-pack: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        box-align: center;
    }
    .am-header h1 {
        -webkit-box-flex: 1;
        -ms-flex: 1;
        box-flex: 1;
        line-height: 18px;
        text-align: center;
        font-size: 18px;
        font-weight: 300;
        color: #fff;
    }
</style>
</head>
<body text=#000000 bgColor="#ffffff" leftMargin=0 topMargin=4>
<header class="am-header">
        <h1>确认支付信息</h1>
</header>
<div id="main">
        <form name=alipayment action=alipayapi.php method=post target="_blank">
            <div id="body" style="clear:left">
                <dl class="content">
                    <dt>收款账号：</dt>
                    <dd>
                        <input readonly type="text" value="协和华东干细胞基因工程有限公司" />
                        <input  name="WIDshow_url" type="hidden"  value="http://www.chinacire.com.cn/edm/images/1_1443555572.jpg "/>
                    </dd>
                    <hr class="one_line">
                    <dt>商户订单号：</dt>
                    <dd>
                        <input id="WIDout_trade_no" readonly name="WIDout_trade_no" value="20127234567891" />
                    </dd>
                    <input id="WIDsubject" readonly name="WIDsubject" type="hidden" value="测试商品"/>
                    <hr class="one_line">
                    <dt>付款金额：</dt>    
                    <dd>
                        <input id="WIDtotal_fee" readonly name="WIDtotal_fee" value="0.01" />
                    </dd>
                    <hr class="one_line">
                    <dt>收款账号：</dt>
                    <dd>
                        <input readonly type="text" value="慧宇思科技有限公司" />
                    </dd>
                    <hr class="one_line">
                    <dt>付款日期：</dt>
                    <dd>
                        <input id="WIDpay_time" name="WIDpay_time" readonly  value="2017/11/16" />
                    </dd>
                    <hr class="one_line">
                    <dt></dt>
                    <dd id="btn-dd">
                        <span class="new-btn-login-sp">
                            <button class="new-btn-login" type="submit" style="text-align:center;">确 认</button>
                        </span>
                        <span class="note-help">如果您点击“确认”按钮，即表示您同意该次的执行操作。</span>
                    </dd>
                </dl>
            </div>
		</form>
        <div id="foot">
			<ul class="foot-ul">
				<li>
                <!--
					支付宝版权所有 2015-2018 ALIPAY.COM 
                -->    
				</li>
			</ul>
		</div>
	</div>
</body>
<script language="javascript">
	function GetDateNow() {
		var vNow = new Date();
		var sNow = "";
		sNow += String(vNow.getFullYear());
		sNow += String(vNow.getMonth() + 1);
		sNow += String(vNow.getDate());
		sNow += String(vNow.getHours());
		sNow += String(vNow.getMinutes());
		sNow += String(vNow.getSeconds());
		sNow += String(vNow.getMilliseconds());
		//document.getElementById("WIDout_trade_no").value =  sNow;
		//document.getElementById("WIDsubject").value = "协和华东在线支付";   
		//document.getElementById("WIDtotal_fee").value = "0.01";
	}
	GetDateNow();
</script> 
</html>