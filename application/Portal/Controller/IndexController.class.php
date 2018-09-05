<?php
namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
  * 首页
  */
  class IndexController extends HomebaseController
  {
  	
  	function index()
  	{
      $list = M('goods')->field('id,title,smeta')->select();
      foreach ($list as $key => $value) {
        $list[$key]['smeta'] = json_decode($value['smeta'])->thumb;
      }
      $this->assign('list',$list);
  		$this->display(':index');
        
  	}

  }
  