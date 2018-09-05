<?php
/**
 * Created by PhpStorm.
 * User: PVer
 * Date: 2017/5/5
 * Time: 10:32
 */
  session_start();
 require_once(dirname(__FILE__)."/../../include/common.inc.php");

 $pay=$_POST['pay'];
 $id_card=$_POST['identity'];
 $name=$_POST['name'];
 $province=$_POST['province'];
 $city=$_POST['city'];
 $county=$_POST['county'];
 $address=$_POST['address'];
 $birth=$_POST['birthday'];
$tel=$_POST['tel'];
$remark=$_POST['info'];
$add_time=time();
$randoms=$_POST['randoms'];
$order_sn=time().rand(1,9999);
    $sql="INSERT INTO `dede_online_payment`
            (code,pay_name,amount,id_card,name,province,city,area,address,birth,tel,remark,enabled,order_sn,add_time)
            VALUES
            ('alipay_wap','支付宝手机支付','$pay','$id_card','$name','$province','$city','$county','$address','$birth','$tel','$remark','0','$order_sn','$add_time')";
    $dsql->ExecuteNoneQuery($sql);
    $id=mysql_insert_id();
    $_SESSION['pay_insert_id']=$id;

  header("location:index.php");

