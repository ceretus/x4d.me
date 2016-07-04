<?php
namespace Material\Controller;

use Home\Controller\BaseController;

class IndexController extends BaseController
{
    public function index()
    {
        $material = array();
        $materialCategory = list_to_tree(M('MaterialCategory')->field('id,pid,name,en,sort')->order('sort asc')->select());
        $material['cate'] = $materialCategory;
        $this->assign('material', $material);
        $this->display();
    }

    public function lists()
    {
        $this->display();
    }

    public function info($id = null)
    {
        $materialModel = M('Material');
        $material = $materialModel->where(array('id' => intval($id)))->find();
        $material || $this->_empty();
        $materialModel->where("id=". intval($id))->setInc('click',1);
        $this->assign('material', $material);
        $this->display();
    }
}