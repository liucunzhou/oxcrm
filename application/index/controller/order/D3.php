<?php

namespace app\index\controller\order;


use app\common\model\OrderD3;
use app\index\controller\Backend;

class D3 extends Backend
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new OrderD3();

        ## 获取所有品牌、公司
        $brands = \app\common\model\Brand::getBrands();
        $this->assign('brands', $brands);


        $rituals = \app\common\model\Ritual::getList();
        $this->assign('rituals', $rituals);

        $packages = \app\common\model\Package::getList();
        $this->assign('packages', $packages);

        $d3List = \app\common\model\D3::getList();
        $this->assign('d3List', $d3List);

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
        foreach ($params['d3_id'] as $key=>$val) {
            $data = [];
            $data['salesman'] = $params['salesman'];
            $data['is_suborder'] = $params['is_suborder'];
            $data['operate_id'] = $this->user['id'];
            $data['order_id'] = $params['order_id'];
            $data['d3_id'] = $val;
            $data['d3_amount'] = $params['amount'][$key];
            $data['d3_price'] = $params['price'][$key];
            $data['d3_contact'] = $params['contact'][$key];
            $data['d3_mobile'] = $params['mobile'][$key];
            $data['d3_address'] = $params['address'][$key];
            $data['d3_remark'] = $params['remark'][$key];
            $d3Order = new OrderD3();
            $d3Order->save($data);
        }

        return json(['code'=>'200', 'msg'=>'添加3D信息成功']);
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