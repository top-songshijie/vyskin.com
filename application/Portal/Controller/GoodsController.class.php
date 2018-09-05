<?php
/**
 * Created by PhpStorm.
 * User: Jie
 * Date: 2017/12/08
 * Time: 11:21
 */
namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
* 商品
*/
class GoodsController extends HomebaseController
{
	//商品列表
	public function goodsList()
	{
		$list = M('goods')->field('id,title,smeta')->select();
		foreach ($list as $key => $value) {
        $list[$key]['smeta'] = json_decode($value['smeta'])->thumb;
        }
		$this->assign('list',$list);
		$this->display('Goods/goodsList');
	}

	//商品详情
	public function goodsDetail()
	{
		//商品id
        unset($_SESSION['datazhijiecar']);
		$id=I('get.id');
		$goods=M('goods')->where("id=$id")->find();
//		dump($goods);exit();
		$this->assign('goods',$goods);
		$this->display('Goods/goodsDetail');
	}

}
