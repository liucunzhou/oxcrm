<?php

namespace app\wash\controller\dictionary;

use app\wash\controller\Backend;

class Region extends Backend
{
    protected function initialize(){
        parent::initialize();
        $this->model = new \app\common\model\Region();
    }

    public function areas($id){
        $where = [];
        $where['pid'] = $id;
        $list = $this->model->where($where)->select();
        $this->assign('list', $list);

        return $this->fetch();

    }
}
