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

    }

    public function doCreate()
    {

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
        $param = $param['incomeList'];
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
                'remark' => $param['wedding_income_remark']
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