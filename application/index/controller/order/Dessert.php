<?php

namespace app\index\controller\order;


use app\common\model\OrderDessert;
use app\index\controller\Backend;

class Dessert extends Backend
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new OrderDessert();

        ## 获取所有品牌、公司
        $brands = \app\common\model\Brand::getBrands();
        $this->assign('brands', $brands);


        $rituals = \app\common\model\Ritual::getList();
        $this->assign('rituals', $rituals);

        $packages = \app\common\model\Package::getList();
        $this->assign('packages', $packages);

        $dessertList = \app\common\model\Dessert::getList();
        $this->assign('dessertList', $dessertList);
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

    public function doCreate()
    {
        $params = $this->request->param();
        foreach ($params['dessert_id'] as $key=>$val) {
            $data = [];
            $data['operate_id'] = $this->user['id'];
            $data['order_id'] = $params['order_id'];
            $data['dessert_id'] = $val;
            $data['dessert_amount'] = $params['amount'][$key];
            $data['dessert_price'] = $params['price'][$key];
            $data['dessert_contact'] = $params['contact'][$key];
            $data['dessert_mobile'] = $params['mobile'][$key];
            $data['dessert_address'] = $params['address'][$key];
            $data['dessert_remark'] = $params['remark'][$key];
            $dessertOrder = new OrderDessert();
            $dessertOrder->save($data);
        }

        return json(['code'=>'200', 'msg'=>'添加点心信息成功']);
    }

    public function edit($id)
    {

        $where = [];
        $where[] = ['id', '=', $id];
        $order = $this->model->where($where)->order('id desc')->find();
        $this->assign('data', $order);

        return $this->fetch();
    }

    public function doEdit()
    {
        $params = $this->request->param();

        $where = [];
        $where[] = ['id', '=', $params['id']];
        $banquet = $this->model->where($where)->find();

        $result = $banquet->save($params);

        if($result) {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}