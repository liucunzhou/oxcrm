<?php

namespace app\h5\controller\order;

use app\common\model\OrderBanquetPayment;
use app\common\model\OrderWeddingPayment;
use app\h5\controller\Base;

class Payment extends Base
{
    protected $paymentTypes = [1=>'定金', 2=>'中款', 3=>'尾款', 4=>'意向金', 5=>'二销'];
    protected function initialize()
    {
        parent::initialize();
    }

    public function create()
    {
        $param = $this->request->param();
        $order = \app\common\model\Order::get($param['order_id']);
        if (empty($order)) {
            $result = [
                'code' => '400',
                'msg' => '订单不存在'
            ];
            return json($result);
        }

        if (empty($order->company_id)) {
            $result = [
                'code' => '400',
                'msg' => '未设置签约公司'
            ];
            return json($result);
        }

        $where = [];
        $where[] = ['company_id', '=', $order->company_id];
        $where[] = ['timing', '=', 'payment'];
        $audit = \app\common\model\Audit::where($where)->find();

        if (empty($audit)) {
            $result = [
                'code' => '400',
                'msg' => '尚未设置审核顺序'
            ];
            return json($result);
        }

        if (empty($audit->content)) {
            $result = [
                'code' => '400',
                'msg' => '尚未设置审核顺序'
            ];
            return json($result);
        }

        $avatar = 'https://www.yusivip.com/upload/commonAppimg/hs_app_logo.png';
        $staffs = \app\common\model\User::getUsers(false);
        ## 审核全局列表
        $sequence = $this->config['check_sequence'];
        $auth = json_decode($audit->content, true);
        $confirmList = [];
        foreach ($auth as $key => $row) {
            $managerList = [];
            $type = $sequence[$key]['type'];
            if ($type == 'role') {
                // 获取角色
                foreach ($row as $v) {
                    $user = \app\common\model\User::getRoleManager($v, $this->user);
                    $managerList[] = [
                        'id' => $user['id'],
                        'realname' => $user['realname'],
                        'avatar' => $user['avatar'] ? $user['avatar'] : $avatar
                    ];
                }
            } else {
                foreach ($row as $v) {
                    if (!isset($staffs[$v])) continue;
                    $user = $staffs[$v];
                    $managerList[] = [
                        'id' => $user['id'],
                        'realname' => $user['realname'],
                        'avatar' => $user['avatar'] ? $user['avatar'] : $avatar
                    ];
                }
            }
            $confirmList[] = [
                'id' => $key,
                'title' => $sequence[$key]['title'],
                'managerList' => $managerList
            ];
        }

        $result = [
            'code' => '200',
            'msg' => '获取数据成功',
            'data' => [
                'incomePaymentList' => $this->config['payments'],
                'incomeTypeList' => $this->config['payment_type_list'],
                'confirmList' => $confirmList
            ]
        ];
        return json($result);
    }

    public function doCreate()
    {
        $param = $this->request->param();
        $order = \app\common\model\Order::where('id', '=', $param['order_id'])->find();
        $data = json_decode($param['paymentList'], true);
        $paymentValidate = new \app\common\validate\OrderPayment();
        if(!$paymentValidate->check($data)) {
            return json([
                'code' => '400',
                'msg' => $paymentValidate->getError()
            ]);
        }

        if ($order->news_type == 1) {
            $payment['order_id'] = $param['order_id'];
            $payment['user_id'] = $this->user['id'];
            $payment['wedding_payment_no'] = $data['payment_no'];
            $payment['wedding_pay_type'] = $data['pay_type'];
            $payment['wedding_apply_pay_date'] = $data['apply_pay_date'];
            $payment['wedding_pay_item_price'] = $data['pay_item_price'];
            $payment['wedding_payment_remark'] = $data['payment_remark'];
            $payment['wedding_pay_to_company'] = $data['pay_to_company'];
            $payment['wedding_pay_to_account'] = $data['pay_to_account'];
            $payment['wedding_pay_to_bank'] = $data['pay_to_bank'];
            $payment['receipt_img'] = implode(',',$data['receipt_img']);
            $payment['note_img'] = implode(',', $data['note_img']);
            $paymentModel = new OrderWeddingPayment();
            $result2 = $paymentModel->allowField(true)->save($payment);
            $type = $this->paymentTypes[$data['pay_type']];
            $intro = "创建婚庆{$type}付款";
            $source['weddingPayment'][] = $paymentModel->toArray();
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'payment', $intro, $source);
        } else {
            // 添加收款信息
            // $data = json_decode($param['paymentList'], true);
            $payment['order_id'] = $param['order_id'];
            $payment['user_id'] = $this->user['id'];
            $payment['banquet_payment_no'] = $data['payment_no'];
            $payment['banquet_pay_type'] = $data['pay_type'];
            $payment['banquet_apply_pay_date'] = $data['apply_pay_date'];
            $payment['banquet_pay_item_price'] = $data['pay_item_price'];
            $payment['banquet_payment_remark'] = $data['payment_remark'];
            $payment['banquet_pay_to_company'] = $data['pay_to_company'];
            $payment['banquet_pay_to_account'] = $data['pay_to_account'];
            $payment['banquet_pay_to_bank'] = $data['pay_to_bank'];
            $payment['receipt_img'] = implode(',',$data['receipt_img']);
            $payment['note_img'] = implode(',', $data['note_img']);
            $paymentModel = new OrderBanquetPayment();
            $result2 = $paymentModel->allowField(true)->save($payment);
            $type = $this->paymentTypes[$data['pay_type']];
            $intro = "创建婚宴{$type}付款";
            $source['banquetPayment'][] = $paymentModel->toArray();
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'payment', $intro, $source);
        }

        if ($result2) {
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
        if ($param['income_category'] == '婚宴') {
            $model = new OrderBanquetPayment();
        } else {
            $model = new OrderWeddingPayment();
        }

        $row = $model->where('id', '=', $param['id'])->find();
        if (empty($row)) {
            $result = [
                'code' => '400',
                'msg' => '读取失败'
            ];

            return json($result);
        }

        $payTypeList = array_column($this->config['payment_type_list'], 'title', 'id');
        if ($param['income_category'] == '婚宴') {
            $data = [
                'id' => $row->id,
                'payment_no' => $row->banquet_payment_no,
                'pay_type' => $row->banquet_pay_type,
                'pay_type_text' => $payTypeList[$row->banquet_pay_type],
                'apply_pay_date' => $row->banquet_apply_pay_date,
                'pay_real_date' => $row->banquet_pay_real_date,
                'pay_item_price' => $row->banquet_pay_item_price,
                'payment_remark' => $row->banquet_payment_remark,
                'pay_to_company' => $row->banquet_pay_to_company,
                'pay_to_account' => $row->banquet_pay_to_account,
                'pay_to_bank' => $row->banquet_pay_to_bank,
                'receipt_img' => $row->receipt_img,
                'note_img' => $row->note_img,
                'pay_category' => $param['income_category'],
                'item_check_status' => 0
            ];
        } else {
            $data = [
                'id' => $row->id,
                'payment_no' => $row->wedding_payment_no,
                'pay_type' => $row->wedding_pay_type,
                'pay_type_text' => $payTypeList[$row->wedding_pay_type],
                'apply_pay_date' => $row->wedding_apply_pay_date,
                'pay_real_date' => $row->wedding_pay_real_date,
                'pay_item_price' => $row->wedding_pay_item_price,
                'payment_remark' => $row->wedding_payment_remark,
                'pay_to_company' => $row->wedding_pay_to_company,
                'pay_to_account' => $row->wedding_pay_to_account,
                'pay_to_bank' => $row->wedding_pay_to_bank,
                'receipt_img' => $row->receipt_img,
                'note_img' => $row->note_img,
                'pay_category' => $param['income_category'],
                'item_check_status' => 0
            ];
        }

        $result = [
            'code' => '200',
            'msg' => '读取成功',
            'data' => [
                'paymentList' => $data,
                'payTypeList' => $this->config['payment_type_list'],
            ]
        ];
        return json($result);
    }

    public function doEdit()
    {
        $param = $this->request->param();
        $param = json_decode($param['paymentList'], true);

        $order = \app\common\model\Order::get($param['order_id']);
        $paymentValidate = new \app\common\validate\OrderPayment();
        if(!$paymentValidate->check($param)) {
            return json([
                'code' => '400',
                'msg' => $paymentValidate->getError()
            ]);
        }

        if ($order['news_type'] != 1) {
            $model = new OrderBanquetPayment();
        } else {
            $model = new OrderWeddingPayment();
        }
        $row = $model->where('id', '=', $param['id'])->find();
        if (empty($row)) {
            $result = [
                'code' => '400',
                'msg' => '读取失败'
            ];
            return json($result);
        }

        if ($order['news_type'] != 1) {
            $data = [
                'id' => $param['id'],
                'banquet_payment_no' => $param['payment_no'],
                'banquet_pay_type' => $param['pay_type'],
                'banquet_apply_pay_date' => $param['apply_pay_date'],
                'banquet_pay_item_price' => $param['pay_item_price'],
                'banquet_payment_remark' => $param['payment_remark'],
                'banquet_pay_to_company' => $param['pay_to_company'],
                'banquet_pay_to_account' => $param['pay_to_account'],
                'banquet_pay_to_bank' => $param['pay_to_bank'],
                'receipt_img' => implode(',', $param['receipt_img']),
                'note_img' => implode(',', $param['note_img']),
                'item_check_status' => 0
            ];
        } else {
            $data = [
                'id' => $param['id'],
                'wedding_payment_no' => $param['payment_no'],
                'wedding_pay_type' => $param['pay_type'],
                'wedding_apply_pay_date' => $param['apply_pay_date'],
                'wedding_pay_item_price' => $param['pay_item_price'],
                'wedding_payment_remark' => $param['payment_remark'],
                'wedding_pay_to_company' => $param['pay_to_company'],
                'wedding_pay_to_account' => $param['pay_to_account'],
                'wedding_pay_to_bank' => $param['pay_to_bank'],
                'receipt_img' => implode(',', $param['receipt_img']),
                'note_img' => implode(',', $param['note_img']),
                'item_check_status' => 0
            ];
        }
        $rs = $row->allowField(true)->save($data);
        $type = $this->paymentTypes[$param['pay_type']];
        if ($order['news_type'] != 1) {
            $intro = "编辑婚宴{$type}付款";
            $source['banquetPayment'][] = $row->toArray();
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'payment', $intro, $source);
        } else {
            $intro = "编辑婚庆{$type}付款";
            $source['weddingPayment'][] = $row->toArray();
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'payment', $intro, $source);
        }
        if ($rs) {
            $result = [
                'code' => '200',
                'msg' => '编辑成功',
            ];
        } else {
            $result = [
                'code' => '400',
                'msg' => '编辑失败',
            ];
        }

        return json($result);
    }
}