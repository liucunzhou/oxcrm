<?php

namespace app\index\controller\order;

use app\common\model\OrderHotelItem;
use app\h5\controller\Base;

class HotelItem extends Base
{
    protected $model = null;
    public function initialize()
    {
        parent::initialize();
        $this->model = new OrderHotelItem();
    }

    public function create()
    {
        $param = $this->request->param();
        $order = new \app\common\model\Order();
        $where = [];
        $where['id'] = $param['id'];
        $row = $order->where($where)->find();
        $this->assign('order', $row);

        return $this->fetch();
    }

    public function edit($id)
    {
        $where = [];
        $where[] = ['id', '=', $id];
        $data = $this->model->where($where)->find();

        if($data) {
            $result = [
                'code' => '200',
                'msg' => '获取数据成功',
                'data' => [
                    'detail' => $data
                ]
            ];
        } else {
            $result = [
                'code' => '400',
                'msg' => '获取数据失败'
            ];
        }
        return json($result);
    }

    public function doEdit()
    {
        $param = $this->request->param();
        $param = json_decode($param['hotelItem'], true);
        if(empty(!$param['id'])) {
            $where = [];
            $where[] = ['id', '=', $param['id']];
            $model = $this->model->where($where)->find();
            $result = $model->save($param);
        } else {
            $result = $this->model->allowField(true)->save($param);
        }

        if($result) {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}