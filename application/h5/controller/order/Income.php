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

        if($param['income_category'] == '婚宴') {
            $data = [
                'id'    => $row->id,
                'receivable_no' => $row->banquet_receivable_no,
                'income_payment'    => $row->banquet_income_payment,
                'income_type'   => $row->banquet_income_type,
                'income_date'   => $row->banquet_income_date,
                'income_real_date'  => $row->banquet_income_real_date,
                'income_remark' => $row->banquet_income_remark
            ];
        } else {
            $data = [
                'id'    => $row->id,
                'receivable_no' => $row->wedding_receivable_no,
                'income_payment'    => $row->wedding_income_payment,
                'income_type'   => $row->wedding_income_type,
                'income_date'   => $row->wedding_income_date,
                'income_real_date'  => $row->wedding_income_real_date,
                'income_remark' => $row->wedding_income_remark
            ];
        }

        $result = [
            'code'  => '400',
            'msg'   => '读取失败',
            'data'  => [
                'income'   =>   $data
            ]
        ];

        return json($result);
    }

    public function doEdit()
    {

    }
}