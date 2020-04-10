<?php

namespace app\index\controller\order;


use app\common\model\OrderWedding;
use app\index\controller\Backend;

class Wedding extends Backend
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new OrderWedding();

        ## 获取所有品牌、公司
        $brands = \app\common\model\Brand::getBrands();
        $this->assign('brands', $brands);

        $rituals = \app\common\model\Ritual::getList();
        $this->assign('rituals', $rituals);

        $packages = \app\common\model\Package::getList();
        $this->assign('packages', $packages);
    }

    public function create()
    {
        $params = $this->request->param();
        $order = new \app\common\model\Order();
        $where = [];
        $where['id'] = $params['id'];
        $row = $order->where($where)->find();
        $this->assign('order', $row);

        return $this->fetch();
    }

    public function edit($id)
    {
        $where = [];
        $where[] = ['order_id', '=', $id];
        $order = $this->model->where($where)->order('id desc')->find();
        $this->assign('data', $order);

        return $this->fetch();
    }

    public function doEdit()
    {
        $params = $this->request->param();

        if(empty(!$params['id'])) {
            $where = [];
            $where[] = ['id', '=', $params['id']];
            $model = $this->model->where($where)->find();
            $result = $model->save($params);
        } else {
            $result = $this->model->allowField(true)->save($params);
        }

        if($result) {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}