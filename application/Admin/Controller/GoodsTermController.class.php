<?php
/**
 * 商品分类
 * Created by PhpStorm.
 * User: TigerYang
 * Date: 2017/9/4
 * Time: 14:22
 */

namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class GoodsTermController extends AdminbaseController {
    protected $terms_model;

    function _initialize() {
        parent::_initialize();
        $this->terms_model = D("GoodsTerm");
    }

    // 后台商品分类列表
    public function index(){
        $result = $this->terms_model->order(array("listorder"=>"asc"))->select();

        $tree = new \Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($result as $r) {
            $r['str_manage'] = '<a href="' . U("GoodsTerm/add", array("parent" => $r['term_id'])) . '">添加子类</a> | <a href="' . U("GoodsTerm/edit", array("id" => $r['term_id'])) . '">'.L('EDIT').'</a> | <a class="js-ajax-delete" href="' . U("GoodsTerm/delete", array("id" => $r['term_id'])) . '">'.L('DELETE').'</a> ';
            $url=U('portal/list/index',array('id'=>$r['term_id']));
            $r['url'] = $url;
            $r['id']=$r['term_id'];
            $r['parentid']=$r['parent'];
            $array[] = $r;
        }

        $tree->init($array);
        $str = "<tr>
					<td><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input input-order'></td>
					<td>\$id</td>
					<td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
					<td>\$str_manage</td>
				</tr>";
        $taxonomys = $tree->get_tree(0, $str);
        $this->assign("taxonomys", $taxonomys);
        $this->display();
    }

    // 商品分类添加
    public function add(){
        if (IS_POST) {
            if ($this->terms_model->create()!==false) {
                if ($this->terms_model->add()!==false) {
                    F('goods_terms',null);
                    $this->success("添加成功！",U("GoodsTerm/index"));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->terms_model->getError());
            }
        }else{
            $parentid = I("get.parent",0,'intval');
            $tree = new \Tree();
            $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            $terms = $this->terms_model->order(array("path"=>"asc"))->select();

            $new_terms=array();
            foreach ($terms as $r) {
                $r['id']=$r['term_id'];
                $r['parentid']=$r['parent'];
                $r['selected']= (!empty($parentid) && $r['term_id']==$parentid)? "selected":"";
                $new_terms[] = $r;
            }
            $tree->init($new_terms);
            $tree_tpl="<option value='\$id' \$selected>\$spacer\$name</option>";
            $tree=$tree->get_tree(0,$tree_tpl);

            $this->assign("terms_tree",$tree);
            $this->assign("parent",$parentid);
            $this->display();
        }
    }
    
    // 商品分类编辑
    public function edit(){
        if (IS_POST) {
            if ($this->terms_model->create()!==false) {
                if ($this->terms_model->save()!==false) {
                    F('goods_terms',null);
                    $this->success("修改成功！");
                } else {
                    $this->error("修改失败！");
                }
            } else {
                $this->error($this->terms_model->getError());
            }
        }else{
            $id = I("get.id",0,'intval');
            $data=$this->terms_model->where(array("term_id" => $id))->find();
            $tree = new \Tree();
            $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            $terms = $this->terms_model->where(array("term_id" => array("NEQ",$id), "path"=>array("notlike","%-$id-%")))->order(array("path"=>"asc"))->select();
            $new_terms=array();
            foreach ($terms as $r) {
                $r['id']=$r['term_id'];
                $r['parentid']=$r['parent'];
                $r['selected']=$data['parent']==$r['term_id']?"selected":"";
                $new_terms[] = $r;
            }

            $tree->init($new_terms);
            $tree_tpl="<option value='\$id' \$selected>\$spacer\$name</option>";
            $tree=$tree->get_tree(0,$tree_tpl);

            $this->assign("terms_tree",$tree);
            $this->assign("data",$data);
            $this->display();
        }
        
    }


    // 商品分类排序
    public function listorders() {
        $status = parent::_listorders($this->terms_model);
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }

    // 删除商品分类
    public function delete() {
        $id = I("get.id",0,'intval');
        $count = $this->terms_model->where(array("parent" => $id))->count();

        if ($count > 0) {
            $this->error("该菜单下还有子类，无法删除！");
        }

        if ($this->terms_model->delete($id)!==false) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }
}