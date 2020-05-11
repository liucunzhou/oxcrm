<?php

namespace app\h5\controller\order;


use app\common\model\Brand;
use app\common\model\OrderBanquet;
use app\common\model\Package;
use app\common\model\Ritual;
use app\h5\controller\Base;

class Banquet extends Base
{
    protected $model = null;

    public function initialize()
    {
        parent::initialize();
        $this->model = new OrderBanquet();
    }

    public function create()
    {
        $param = $this->request->param();
        $packageList = Package::getList();
        $ritualList = Ritual::getList();
        $companyList = Brand::getBrands();

        $order = \app\common\model\Order::get($param['order_id']);
        $confirmList = $this->getConfirmProcess($order->company_id, 'order');

        $result = [
            'code'  => '200',
            'msg'   => '获取婚宴信息成功',
            'data'  => [
                'packageList' => array_values($packageList),
                'ritualList' => array_values($ritualList),
                'companyList' =>  array_values($companyList),
                'confirmList' => $confirmList
            ]
        ];

        return json($result);
    }

    public function doCreate()
    {
        $params = $this->request->param();
        $params = json_decode($params['banquet'], true);
        $result = $this->model->allowField(true)->save($params);
        $source['banquet'] = $this->model->toArray();

        if ($result) {
            $order = \app\common\model\Order::get($params['order_id']);
            $intro = "创建婚宴信息审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code' => '200', 'msg' => '创建婚宴信息成功'];
        } else {
            $arr = ['code' => '400', 'msg' => '创建婚宴信息失败'];
        }

        return json($arr);
    }

    public function edit($id)
    {
        $fields = "create_time,delete_time,update_time";
        $where = [];
        $where[] = ['id', '=', $id];
        $data = $this->model->field($fields, true)->where($where)->order('id desc')->find();
        if(empty($data)) {
            $result = [
                'code' => '400',
                'msg' => '获取数据失败'
            ];
            return json($result);
        }
        $data = $data->getData();

        $packageList = Package::getList();
        $ritualList = Ritual::getList();
        $companyList = Brand::getBrands();

        $data['banquet_package_title'] = $packageList[$data['banquet_package_id']]['title'];
        $data['banquet_ritual_title'] = $ritualList[$data['banquet_ritual_id']]['title'];
        $data['company_title'] = $companyList[$data['company_id']]['title'];
        if ($data) {
            $result = [
                'code'  => '200',
                'msg'   => '获取婚宴信息成功',
                'data'  => [
                    'banquet'   => $data,
                    'packageList' => array_values($packageList),
                    'ritualList' => array_values($ritualList),
                    'companyList' =>  array_values($companyList)
                ]
            ];
        } else {
            $result = [
                'code'  => '400',
                'msg'   => '获取婚宴信息失败'
            ];
        }
        return json($result);
    }

    public function doEdit()
    {
        $params = $this->request->param();
        $params = json_decode($params['banquet'], true);
        if (!empty($params['id'])) {
            $where = [];
            $where[] = ['id', '=', $params['id']];
            $model = $this->model->where($where)->find();
            $result = $model->allowField(true)->save($params);
            $source['banquet'] = $model->toArray();
        } else {
            $result = $this->model->allowField(true)->save($params);
            $source['banquet'] = $this->model->toArray();
        }

        if ($result) {
            $order = \app\common\model\Order::get($params['order_id']);
            $intro = "编辑婚宴信息审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code' => '200', 'msg' => '编辑基本信息成功'];
        } else {
            $arr = ['code' => '400', 'msg' => '编辑基本信息失败'];
        }

        return json($arr);
    }
}