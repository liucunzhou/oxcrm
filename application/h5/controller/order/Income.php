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
        $param = $this->request->param();
        $order = \app\common\model\Order::get($param['order_id']);
        if(empty($order)) {
            $result = [
                'code'  => '400',
                'msg'   => '订单不存在'
            ];
            return json($result);
        }

        if(empty($order->company_id)) {
            $result = [
                'code'  => '400',
                'msg'   => '未设置签约公司'
            ];
            return json($result);
        }

        $confirmList = $this->getConfirmProcess($order->company_id, 'income');
        $result = [
            'code' => '200',
            'msg' => '获取数据成功',
            'data' => [
                'incomePaymentList' => $this->config['payments'],
                'incomeTypeList' => $this->config['payment_type_list'],
                'confirmList'   => $confirmList
            ]
        ];
        return json($result);
    }

    public function doCreate()
    {
        $param = $this->request->param();
        $order = \app\common\model\Order::where('id', '=', $param['order_id'])->find();

        if($order->news_type == 1) {
            $data = json_decode($param['incomeList'], true);
            $income['order_id'] = $param['order_id'];
            $income['user_id'] = $this->user['id'];
            $income['wedding_receivable_no'] = $data['receivable_no'];
            $income['wedding_income_payment'] = $data['income_payment'];
            $income['wedding_income_type'] = $data['income_type'];
            $income['wedding_income_date'] = $data['income_date'];
            $income['wedding_income_item_price'] = $data['income_item_price'];
            $income['wedding_income_item_price'] = $data['income_item_price'];
            $income['remark'] = $data['income_remark'];
            $income['receipt_img'] = implode(',', $data['receipt_img']);
            $income['note_img'] = implode(',', $data['note_img']);
            $receivable = new OrderBanquetReceivables();
            $result2 = $receivable->allowField(true)->save($income);
            $source['weddingIncome'][] = $receivable->toArray();
            $intro = '创建婚庆收款审核';
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'income', $intro, $source);
        } else {
            // 添加收款信息
            $data = json_decode($param['incomeList'], true);
            $income['order_id'] = $param['order_id'];
            $income['user_id'] = $this->user['id'];
            $income['banquet_receivable_no'] = $data['receivable_no'];
            $income['banquet_income_payment'] = $data['income_payment'];
            $income['banquet_income_type'] = $data['income_type'];
            $income['banquet_income_date'] = $data['income_date'];
            $income['banquet_income_item_price'] = $data['income_item_price'];
            $income['receipt_img'] = implode(',', $data['receipt_img']);
            $income['remark'] = $data['income_remark'];
            $income['note_img'] = implode(',', $data['note_img']);
            $receivable = new OrderBanquetReceivables();
            $result2 = $receivable->allowField(true)->save($income);
            $source['banquetIncome'][] = $receivable->toArray();
            $intro = '创建婚宴收款审核';
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'income', $intro, $source);
        }

        if($result2) {
            $result = [
                'code' => '200',
                'msg' => '添加收款成功'
            ];
        } else {
            $result = [
                'code' => '400',
                'msg' => '添加收款失败'
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
                'income_remark' => $row->remark,
                'receipt_img' => $row->receipt_img,
                'note_img' => $row->note_img,
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
                'receipt_img' => $row->receipt_img,
                'note_img' => $row->note_img,
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
        $order = \app\common\model\Order::get($param['order_id']);

        if($order->news_type=='1') {
            $model = new OrderWeddingReceivables();
        } else {
            $model = new OrderBanquetReceivables();
        }

        $row = $model->where('id', '=', $param['id'])->find();
        if(empty($row)) {
            $result = [
                'code'  => '400',
                'msg'   => '读取失败'
            ];
            return json($result);
        }

        if($order->news_type != '1') {
            $data = [
                'id'    => $param['id'],
                'banquet_receivable_no' => $param['receivable_no'],
                'banquet_income_payment'    => $param['income_payment'],
                'banquet_income_type'   => $param['income_type'],
                'banquet_income_date'   => $param['income_date'],
                'remark' => $param['income_remark'],
                'receipt_img' => implode(',', $param['receipt_img']),
                'note_img' => implode(',', $param['note_img']),
                'item_check_status' => 0
            ];
        } else {
            $data = [
                'id'    => $param['id'],
                'wedding_receivable_no' => $param['receivable_no'],
                'wedding_income_payment'    => $param['income_payment'],
                'wedding_income_type'   => $param['income_type'],
                'wedding_income_date'   => $param['income_date'],
                'remark' => $param['income_remark'],
                'receipt_img' => implode(',', $param['receipt_img']),
                'note_img' => implode(',', $param['note_img']),
                'item_check_status' => 0
            ];
        }

        $rs = $row->allowField(true)->save($data);
        if($order->news_type != '1') {
            $intro = '编辑婚宴收款审核';
            $source['banquetIncome'][] = $row->toArray();
        } else {
            $intro = '编辑婚庆收款审核';
            $source['weddingIncome'][] = $row->toArray();
        }
        create_order_confirm($order->id, $order->company_id, $this->user['id'], 'income', $intro, $source);

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