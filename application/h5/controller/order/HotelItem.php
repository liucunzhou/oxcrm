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
        if(!empty($param['id'])) {
            $where = [];
            $where[] = ['id', '=', $param['id']];
            $model = $this->model->where($where)->find();
            $result = $model->allowField(true)->save($param);
        } else {
            $result = $this->model->allowField(true)->save($param);
        }

        if($result) {
            $order = \app\common\model\Order::get($param['order_id']);
            $intro = "编辑酒店消费项目审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'income', $intro);
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'400', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}