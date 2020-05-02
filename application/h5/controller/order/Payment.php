<?php
namespace app\h5\controller\order;

use app\common\model\OrderBanquetPayment;
use app\common\model\OrderWeddingPayment;
use app\h5\controller\Base;

class Payment extends Base
{
    protected function initialize()
    {
        parent::initialize();
    }

    public function create()
    {
        $result = [
            'code' => '200',
            'msg' => '获取数据成功',
            'data' => [
                'incomePaymentList' => $this->config['payments'],
                'incomeTypeList' => $this->config['payment_type_list']
            ]
        ];
        return json($result);
    }

    public function doCreate()
    {
        $param = $this->request->param();
        $order = \app\common\model\Order::where('id', '=', $param['order_id'])->find();

        if($order->news_type == 1) {
            $data = json_decode($param['paymentList'], true);
            $payment['order_id'] = $param['order_id'];
            $payment['user_id'] = $this->user['id'];
            $payment['wedding_payment_no'] = $param['payment_no'];
            $payment['wedding_pay_type'] = $data['pay_type'];
            $payment['wedding_apply_pay_date'] = $data['apply_pay_date'];
            $payment['wedding_pay_item_price'] = $data['pay_item_price'];
            $payment['wedding_payment_remark'] = $param['payment_remark'];
            $receivable = new OrderWeddingPayment();
            $result2 = $receivable->allowField(true)->save($payment);
        } else {
            // 添加收款信息
            $data = json_decode($param['paymentList'], true);
            $payment['order_id'] = $param['order_id'];
            $payment['user_id'] = $this->user['id'];
            $payment['banquet_payment_no'] = $param['payment_no'];
            $payment['banquet_pay_type'] = $data['pay_type'];
            $payment['banquet_apply_pay_date'] = $data['apply_pay_date'];
            $payment['banquet_pay_item_price'] = $data['pay_item_price'];
            $payment['banquet_payment_remark'] = $param['payment_remark'];
            $receivable = new OrderBanquetPayment();
            $result2 = $receivable->allowField(true)->save($payment);
        }

        if($result2) {
            $result = [
                'code' => '200',
                'msg' => '添加付款成功'
            ];
        } else {
            $result = [
                'code' => '400',
                'msg' => '添加付款失败'
            ];
        }

        return json($result);
    }

    public function edit()
    {
        $param = $this->request->param();
        if($param['income_category'] == '婚宴') {
            $model = new OrderBanquetPayment();
        } else {
            $model = new OrderWeddingPayment();
        }

        $row = $model->where('id', '=', $param['id'])->find();
        if(empty($row)) {
            $result = [
                'code'  => '400',
                'msg'   => '读取失败'
            ];

            return json($result);
        }

        $payTypeList = array_column($this->config['payment_type_list'], 'title', 'id');
        if($param['income_category'] == '婚宴') {
            $data = [
                'id'    => $row->id,
                'payment_no' => $row->banquet_payment_no,
                'pay_type'   => $row->banquet_pay_type,
                'pay_type_text'   => $payTypeList[$row->banquet_pay_type],
                'apply_pay_date'   => $row->banquet_apply_pay_date,
                'pay_real_date'  => $row->banquet_pay_real_date,
                'pay_item_price'  => $row->banquet_pay_item_price,
                'payment_remark' => $row->banquet_payment_remark,
                'pay_category' => $param['income_category']
            ];
        } else {
            $data = [
                'id'    => $row->id,
                'payment_no' => $row->wedding_payment_no,
                'pay_type'   => $row->wedding_pay_type,
                'pay_type_text'   => $payTypeList[$row->wedding_pay_type],
                'apply_pay_date'   => $row->wedding_apply_pay_date,
                'pay_real_date'  => $row->wedding_pay_real_date,
                'pay_item_price'  => $row->wedding_pay_item_price,
                'payment_remark' => $row->wedding_payment_remark,
                'pay_category' => $param['income_category']
            ];
        }

        $result = [
            'code'  => '200',
            'msg'   => '读取成功',
            'data'  => [
                'paymentList'   =>   $data,
                'payTypeList'    => $this->config['payment_type_list'],
            ]
        ];

        return json($result);
    }

    public function doEdit()
    {
        $param = $this->request->param();
        $param = json_decode($param['paymentList'], true);
        if($param['income_category'] == '婚宴') {
            $model = new OrderBanquetPayment();
        } else {
            $model = new OrderWeddingPayment();
        }

        $row = $model->where('id', '=', $param['id'])->find();
        if(empty($row)) {
            $result = [
                'code'  => '400',
                'msg'   => '读取失败'
            ];

            return json($result);
        }

        if($param['income_category'] == '婚宴') {
            $data = [
                'id'    => $param['id'],
                'banquet_payment_no' => $param['payment_no'],
                'banquet_pay_type'   => $param['pay_type'],
                'banquet_apply_pay_date'   => $param['apply_pay_date'],
                'banquet_pay_item_price'   => $param['pay_item_price'],
                // 'pay_real_date'  => $row->banquet_pay_real_date,
                'banquet_payment_remark' => $param['payment_remark']
            ];
        } else {
            $data = [
                'id'    => $param['id'],
                'wedding_payment_no' => $param['payment_no'],
                'wedding_pay_type'   => $param['pay_type'],
                'wedding_apply_pay_date'   => $param['apply_pay_date'],
                'wedding_pay_item_price'   => $param['pay_item_price'],
                // 'pay_real_date'  => $row->wedding_pay_real_date,
                'wedding_payment_remark' => $param['payment_remark']
            ];
        }
        $rs = $row->allowField(true)->save($data);

        if($rs) {
            $result = [
                'code'  => '200',
                'msg'   => '编辑成功',
            ];
        } else {
            $result = [
                'code'  => '400',
                'msg'   => '编辑失败',
            ];
        }

        return json($result);
    }
}