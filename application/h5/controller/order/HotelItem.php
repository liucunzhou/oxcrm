<?php
namespace app\h5\controller\order;

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
        $order = \app\common\model\Order::get($param['order_id']);
        $confirmList = $this->getConfirmProcess($order->company_id, 'order');

        $result = [
            'code' => '200',
            'msg' => '获取信息成功',
            'data' => [
                'confirmList' => $confirmList
            ]
        ];

        return json($result);
    }

    public function doCreate()
    {

        $param = $this->request->param();
        $orderId = $param['order_id'];
        $param = json_decode($param['hotelItem'], true);
        $param['order_id'] = $orderId;
        $param['user_id'] = $this->user['id'];
        $result = $this->model->allowField(true)->save($param);
        $source['hotelItem'] = $this->model->toArray();

        if($result) {
            $order = \app\common\model\Order::get($orderId);
            $intro = "添加酒店服务项目审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'添加酒店服务项目成功'];
        } else {
            $arr = ['code'=>'400', 'msg'=>'添加酒店服务项目失败'];
        }
        return json($arr);
    }

    public function edit($id)
    {
        $where = [];
        $where[] = ['id', '=', $id];
        $fields = 'id,order_id,wedding_room,part,champagne,tea,cake,cake_amount,champagne_amount,part_amount,wedding_room_amount,tea_amount';
        $data = $this->model->field($fields)->where($where)->find();

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
        $param['item_check_status'] = 0;
        unset($param['create_time']);
        unset($param['update_time']);
        unset($param['delete_time']);

        $where = [];
        $where[] = ['id', '=', $param['id']];
        $model = $this->model->where($where)->find();
        $result = $model->allowField(true)->save($param);
        $source['hotelItem'] = $model->toArray();

        if($result) {
            $order = \app\common\model\Order::get($param['order_id']);
            $intro = "编辑酒店服务项目审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'编辑酒店服务项目成功'];
        } else {
            $arr = ['code'=>'400', 'msg'=>'编辑酒店服务项目失败'];
        }

        return json($arr);
    }
}