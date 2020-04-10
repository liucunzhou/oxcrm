<?php

namespace app\index\controller\order;


use app\common\model\OrderSugar;
use app\index\controller\Backend;

class Sugar extends Backend
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new OrderSugar();

        ## 获取所有品牌、公司
        $brands = \app\common\model\Brand::getBrands();
        $this->assign('brands', $brands);

        $rituals = \app\common\model\Ritual::getList();
        $this->assign('rituals', $rituals);

        $packages = \app\common\model\Package::getList();
        $this->assign('packages', $packages);

        $sugarList = \app\common\model\Sugar::getList();
        $this->assign('sugarList', $sugarList);
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
       foreach ($params['sugar_id'] as $key=>$val) {
           $data = [];
           $data['operate_id'] = $this->user['id'];
           $data['order_id'] = $params['order_id'];
           $data['sugar_id'] = $val;
           $data['sugar_amount'] = $params['amount'][$key];
           $data['sugar_price'] = $params['price'][$key];
           $data['sugar_contact'] = $params['contact'][$key];
           $data['sugar_mobile'] = $params['mobile'][$key];
           $data['sugar_address'] = $params['address'][$key];
           $data['sugar_remark'] = $params['remark'][$key];
           $sugarOrder = new OrderSugar();
           $sugarOrder->save($data);
       }

       return json(['code'=>'200', 'msg'=>'添加喜糖信息成功']);
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