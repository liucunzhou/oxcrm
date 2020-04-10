<?php

namespace app\index\controller\order;


use app\common\model\OrderLight;
use app\index\controller\Backend;

class Light extends Backend
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new OrderLight();

        ## 获取所有品牌、公司
        $brands = \app\common\model\Brand::getBrands();
        $this->assign('brands', $brands);


        $rituals = \app\common\model\Ritual::getList();
        $this->assign('rituals', $rituals);

        $packages = \app\common\model\Package::getList();
        $this->assign('packages', $packages);

        $lightList = \app\common\model\Light::getList();
        $this->assign('lightList', $lightList);

        $staffs = \app\common\model\User::getUsers();
        $this->assign('staffs', $staffs);
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
        foreach ($params['light_id'] as $key=>$val) {
            $data = [];
            $data['salesman'] = $params['salesman'];
            $data['is_suborder'] = $params['is_suborder'];
            $data['operate_id'] = $this->user['id'];
            $data['order_id'] = $params['order_id'];
            $data['light_id'] = $val;
            $data['light_amount'] = $params['amount'][$key];
            $data['light_price'] = $params['price'][$key];
            $data['light_contact'] = $params['contact'][$key];
            $data['light_mobile'] = $params['mobile'][$key];
            $data['light_address'] = $params['address'][$key];
            $data['light_remark'] = $params['remark'][$key];
            $lightOrder = new OrderLight();
            $lightOrder->save($data);
        }

        return json(['code'=>'200', 'msg'=>'添加灯光信息成功']);
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