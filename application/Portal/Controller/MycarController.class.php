<?php
namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
* 购物袋
*/
class MycarController extends HomebaseController
{
	/**
	 * 我的购物袋
	 */
	public function index()
	{
        $userid = $_SESSION['user']['id'];
        if(empty($userid)){
        	$this->error('您还没有登录');exit(); 
        }
		$list['data']=M('cart as c')
		->field('c.id,goods_id,c.attribute,c.price,c.number,g.title,g.smeta')
		->join('cmf_goods as g on g.id=c.goods_id')
		->where("c.userid=$userid")->select();
		//初始化总价格
		$total_price = 0;
		foreach ($list['data'] as $key => $value) {
			//缩略图
			$list['data'][$key]['smeta'] = json_decode($list['data'][$key]['smeta']);
			$list['data'][$key]['smeta'] = $list['data'][$key]['smeta']->thumb;
			//规格
			$list['data'][$key]['attribute'] = json_decode($list['data'][$key]['attribute']);
			$list['data'][$key]['attribute'] = $list['data'][$key]['attribute'][1];
			$list['data'][$key]['small_price'] = $list['data'][$key]['number']*$list['data'][$key]['price'];
			$total_price += $list['data'][$key]['small_price'];
		}
		$list['total_price'] = $total_price;
		$this->assign('list',$list);
		// dump($list);
		$this->display();
	}


	/**
	 *购物车加减操作
	*/
	public function editCar()
	{
		$userid = $_SESSION['user']['id'];
        if(empty($userid)){
            $this->ajaxReturn(array('code'=>400,'msg'=>'没有登录')); exit();
        }
		$id = I('id','');//购物车id
		if(empty($id)) {
		 	$this->ajaxReturn(array('code'=>400,'msg'=>'缺少购物车id'));exit();
		}
		$caozuo = I('caozuo',0);
		if($caozuo == '0') {
			$number = M('cart')->where("id=$id")->getField('number');
			if($number == 1) {
				$res = M('cart')->where("id=$id")->delete();
			}else{
				$res = M('cart')->where("id=$id")->setDec('number',1);
			}		
		}elseif ($caozuo == 1) {
			$res = M('cart')->where("id=$id")->setInc('number',1);
		}else{
			$this->ajaxReturn(array('code'=>400,'msg'=>'出现错误'));
		}
		if($res) {
			$info = M('cart')->where("id=$id")->find();
			$this->ajaxReturn(array('code'=>200,'msg'=>'操作成功','data'=>$info));
		}else {
			$this->ajaxReturn(array('code'=>400,'msg'=>'出现错误'));
		}
	}

	/**
	 * 加入购物车
	 */
	public function addcart()
	{
		//用户id
		$userid = $_SESSION['user']['id'];
		if(empty($userid)){
		   $this->ajaxReturn(array('code'=>400,'msg'=>'没有登录'));exit();
        }
		//商品id
		$goods_id = I('post.goods_id');
		//商品数量
		$number = I('post.number');
		//商品属性及单价
		$select = I('post.select');
		$arr_select = explode(",",$select);
		$price = $arr_select[2];//商品单价
		unset($arr_select[2]);	
		$arr_json = json_encode($arr_select,true);//商品属性json格式
		if($number < 0 or $number==0) {
			$this->ajaxReturn(array('code'=>400,'msg'=>'数量不合理'));exit();
		}
		//判断购物车是否存在
		$if_cunzai = M('cart')->where(array('userid'=>$userid,'goods_id'=>$goods_id,'attribute'=>$arr_json))->find();
		if($if_cunzai){
			//存在，修改数量
			$res = M('cart')->where(array('id'=>$if_cunzai['id']))->setInc('number',$number);
		}else{
			//不存在，插入购物车
			$data['userid'] = $userid;
			$data['goods_id'] = $goods_id;
			$data['number'] = $number;
			$data['price'] = $price;
			$data['attribute'] = $arr_json;
			$data['createtime'] = date("Y-m-d H:i:s",time());
			$res = M('cart')->data($data)->add();			
		}
		if($res) {
			$this->ajaxReturn(array('code'=>200,'msg'=>'添加成功！'));
		}else{
			$this->ajaxReturn(array('code'=>400,'msg'=>'添加失败！'));
		}
	}
	
	public function delCar(){
		//用户id
		$userid = $_SESSION['user']['id'];
		if(empty($userid)){
		   $this->ajaxReturn(array('code'=>400,'msg'=>'没有登录！'));exit();
        }
		//购物车id
		$id = I('id');
		if(empty($id)){
		   $this->ajaxReturn(array('code'=>400,'msg'=>'没有选择购物车中的商品！'));exit();
        }
		
		$res = M('cart')->delete($id);
		if($res){
			$this->ajaxReturn(array('code'=>200,'msg'=>'删除成功！'));exit();
		}else{
			$this->ajaxReturn(array('code'=>400,'msg'=>'删除失败！'));exit();
		}
	}

}
