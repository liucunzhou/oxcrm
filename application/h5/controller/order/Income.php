<?php
namespace app\h5\controller\order;

use app\common\model\OrderBanquetReceivables;
use app\common\model\OrderWeddingReceivables;
use app\h5\controller\Base;

class Income extends Base
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

        if($order->news_type == 2) {
            $income = json_decode($param['banquet_incomeList'], true);
            $income['order_id'] = $param['order_id'];
            $income['banquet_income_type'] = 5;
            $income['remark'] = $param['income_remark'];
            $receivable = new OrderBanquetReceivables();
            $result2 = $receivable->allowField(true)->save($income);
        } else {
            // 添加收款信息
            $data = json_decode($param['banquet_incomeList'], true);

            $income['order_id'] = $param['order_id'];
            $income['banquet_income_payment'] = $data['banquet_income_payment'];
            $income['banquet_income_type'] = $data['banquet_income_type'];
            $income['banquet_income_date'] = $data['banquet_income_date'];
            $income['remark'] = $param['income_remark'];
            $receivable = new OrderBanquetReceivables();
            $result2 = $receivable->allowField(true)->save($income);
        }

        if($result2) {
            $this->model->commit();
            $result = [
                'code' => '200',
                'msg' => '添加婚宴二销成功'
            ];
        } else {
            $this->model->rollback();
            $result = [
                'code' => '400',
                'msg' => '添加婚宴二销失败'
            ];
        }

        return json($result);
    }

    public function edit()
    {
        $param = $this->request->param();
        if($param['income_category'] == '婚宴') {
            $model = new OrderBanquetReceivables();
        } else {
            $model = new OrderWeddingReceivables();
        }

        $row = $model->where('id', '=', $param['id'])->find();
        if(empty($row)) {
            $result = [
                'code'  => '400',
                'msg'   => '读取失败'
            ];

            return json($result);
        }

        $paymentList = array_column($this->config['payments'], 'title', 'id');
        $incomeTypeList = array_column($this->config['payment_type_list'], 'title', 'id');
        if($param['income_category'] == '婚宴') {
            $data = [
                'id'    => $row->id,
                'receivable_no' => $row->banquet_receivable_no,
                'income_payment'    => $row->banquet_income_payment,
                'income_payment_text' => $paymentList[$row->banquet_income_payment],
                'income_type'   => $row->banquet_income_type,
                'income_type_text'   => $incomeTypeList[$row->banquet_income_type],
                'income_date'   => $row->banquet_income_date,
                'income_real_date'  => $row->banquet_income_real_date,
                'income_remark' => $row->banquet_income_remark,
                'income_category' => $param['income_category']
            ];
        } else {
            $data = [
                'id'    => $row->id,
                'receivable_no' => $row->wedding_receivable_no,
                'income_payment'    => $row->wedding_income_payment,
                'income_payment_text' => $paymentList[$row->wedding_income_payment],
                'income_type'   => $row->wedding_income_type,
                'income_type_text'   => $incomeTypeList[$row->wedding_income_type],
                'income_date'   => $row->wedding_income_date,
                'income_real_date'  => $row->wedding_income_real_date,
                'income_remark' => $row->remark,
                'income_category' => $param['income_category']
            ];
        }

        $result = [
            'code'  => '200',
            'msg'   => '读取成功',
            'data'  => [
                'income'   =>   $data,
                'incomeTypeList'    => $this->config['payment_type_list'],
                'incomePaymentList' => $this->config['payments']
            ]
        ];

        return json($result);
    }

    public function doEdit()
    {
        $param = $this->request->param();
        $param = json_decode($param['incomeList'], true);
        if($param['income_category'] == '婚宴') {
            $model = new OrderBanquetReceivables();
        } else {
            $model = new OrderWeddingReceivables();
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
                'banquet_receivable_no' => $param['receivable_no'],
                'banquet_income_payment'    => $param['income_payment'],
                'banquet_income_type'   => $param['income_type'],
                'banquet_income_date'   => $param['income_date'],
                // 'income_real_date'  => $row->banquet_income_real_date,
                'banquet_income_remark' => $param['income_remark']
            ];
        } else {
            $data = [
                'id'    => $param['id'],
                'wedding_receivable_no' => $param['receivable_no'],
                'wedding_income_payment'    => $param['income_payment'],
                'wedding_income_type'   => $param['income_type'],
                'wedding_income_date'   => $param['income_date'],
                // 'income_real_date'  => $row->wedding_income_real_date,
                'remark' => $param['income_remark']
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