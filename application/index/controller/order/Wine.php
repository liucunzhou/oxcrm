<?php

namespace app\index\controller\order;


use app\common\model\OrderWine;
use app\index\controller\Backend;

class Wine extends Backend
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new OrderWine();

        ## 获取所有品牌、公司
        $brands = \app\common\model\Brand::getBrands();
        $this->assign('brands', $brands);


        $rituals = \app\common\model\Ritual::getList();
        $this->assign('rituals', $rituals);

        $packages = \app\common\model\Package::getList();
        $this->assign('packages', $packages);

        $wineList = \app\common\model\Wine::getList();
        $this->assign('wineList', $wineList);
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
        foreach ($params['wine_id'] as $key=>$val) {
            $data = [];
            $data['operate_id'] = $this->user['id'];
            $data['order_id'] = $params['order_id'];
            $data['wine_id'] = $val;
            $data['wine_amount'] = $params['amount'][$key];
            $data['wine_price'] = $params['price'][$key];
            $data['wine_contact'] = $params['contact'][$key];
            $data['wine_mobile'] = $params['mobile'][$key];
            $data['wine_address'] = $params['address'][$key];
            $data['wine_remark'] = $params['remark'][$key];
            $wineOrder = new OrderWine();
            $wineOrder->save($data);
        }

        return json(['code'=>'200', 'msg'=>'添加酒水信息成功']);
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