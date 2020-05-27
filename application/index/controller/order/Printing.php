<?php
namespace app\index\controller\order;

use app\common\model\Audit;
use app\common\model\Brand;
use app\common\model\OrderBanquetPayment;
use app\common\model\OrderBanquetReceivables;
use app\common\model\OrderBanquetSuborder;
use app\common\model\OrderHotelProtocol;
use app\common\model\OrderPrint;
use app\common\model\OrderWeddingPayment;
use app\common\model\OrderWeddingReceivables;
use app\common\model\OrderWeddingSuborder;
use app\common\model\User;
use app\index\controller\Backend;

class Printing extends Backend
{
    public function initialize()
    {
        parent::initialize();

        ## 获取所有品牌、公司
        $brands = \app\common\model\Brand::getBrands();
        $this->assign('brands', $brands);


        $rituals = \app\common\model\Ritual::getList();
        $this->assign('rituals', $rituals);

        $packages = \app\common\model\Package::getList();
        $this->assign('packages', $packages);

        $carList = \app\common\model\Car::getList();
        $this->assign('carList', $carList);

        $sugarList = \app\common\model\Sugar::getList();
        $this->assign('sugarList', $sugarList);

        $wineList = \app\common\model\Wine::getList();
        $this->assign('wineList', $wineList);

        $lightList = \app\common\model\Light::getList();
        $this->assign('lightList', $lightList);

        $dessertList = \app\common\model\Dessert::getList();
        $this->assign('dessertList', $dessertList);

        $ledList = \app\common\model\Led::getList();
        $this->assign('ledList', $ledList);

        $d3List = \app\common\model\D3::getList();
        $this->assign('d3List', $d3List);

        $staffs = \app\common\model\User::getUsers();
        $this->assign('staffs', $staffs);
    }

    // 婚庆表单
    public function payment()
    {
        $param = $this->request->param();
        if ($param['type'] == 'wedding') {
            $model = new OrderWeddingPayment();
        } else {
            $model = new OrderBanquetPayment();
        }

        $payment = $model->get($param['id']);
        if ($param['type'] == 'wedding') {
            $data['apply_pay_date'] = $payment->wedding_apply_pay_date;
            $data['pay_to_company'] = $payment->wedding_pay_to_company;
            $data['pay_to_account'] = $payment->wedding_pay_to_account;
            $data['pay_to_bank'] = $payment->wedding_pay_to_bank;
            $data['pay_item_price'] = $payment->wedding_pay_item_price;
            $data['pay_type'] = $this->paymentTypes[$payment->wedding_pay_type];
            $field = 'wedding_pay_type as pay_type,wedding_pay_item_price as pay_item_price';
        } else {
            $data['apply_pay_date'] = $payment->banquet_apply_pay_date;
            $data['pay_to_company'] = $payment->banquet_pay_to_company;
            $data['pay_to_account'] = $payment->banquet_pay_to_account;
            $data['pay_to_bank'] = $payment->banquet_pay_to_bank;
            $data['pay_item_price'] = $payment->banquet_pay_item_price;
            $data['pay_type'] = $this->paymentTypes[$payment->banquet_pay_type];
            $field = 'banquet_pay_type as pay_type,banquet_pay_item_price as pay_item_price';
        }
        $this->assign('payment', $payment);
        $this->assign('data', $data);

        $where = [];
        $where[] = ['order_id', '=', $payment->order_id];
        $where[] = ['item_check_status', '=', '2'];
        $weddingSuborderTotals = 0;
        // $weddingSuborderTotals = OrderWeddingSuborder::where($where)->sum("wedding_totals");
        $banquetSuborderTotals = OrderBanquetSuborder::where($where)->sum('banquet_totals');
        $suborderTotals = $weddingSuborderTotals + $banquetSuborderTotals;
        $this->assign('suborderTotals', $suborderTotals);

        ### 获取公司信息
        $companies = Brand::getBrands();
        $order = \app\common\model\Order::get($payment->order_id);
        $order['company'] = $companies[$order->company_id]['title'];
        $this->assign('order', $order);

        ### 获取酒店协议实收金额
        $where = [];
        $where[] = ['order_id', '=', $payment->order_id];
        $where[] = ['item_check_status', '=', '2'];
        $protocol = OrderHotelProtocol::where($where)->order('id desc')->find();
        $this->assign('protocol', $protocol);
        $paymentList[1] = $protocol->earnest_money;
        $paymentList[2] = $protocol->middle_money;
        $paymentList[3] = $protocol->tail_money;
        $paymentList[5] = $suborderTotals;

        ### 获取实收列表
        $where = [];
        $where[] = ['order_id', '=', $payment->order_id];
        $where[] = ['item_check_status', '=', '2'];
        $incomeList = [];
        // 婚庆实收列表
        $weddingIncomeList = OrderWeddingReceivables::where($where)->select();

        foreach ($weddingIncomeList as $row) {
            $incomeList[] = [
                'income_type'  => '婚庆'.$this->paymentTypes[$row['wedding_income_type']],
                'income_price'  => $row['wedding_income_item_price'],
            ];
        }
        // 婚宴实收列表
        $banquetIncomeList = OrderBanquetReceivables::where($where)->select();
        foreach ($banquetIncomeList as $row) {
            $incomeList[] = [
                'income_type'  => '婚宴'.$this->paymentTypes[$row['banquet_income_type']],
                'income_price'  => $row['banquet_income_item_price'],
            ];
        }
        $this->assign('incomeList', $incomeList);

        ### 获取实付列表
        $where = [];
        $where[] = ['order_id', '=', $payment->order_id];
        $where[] = ['item_check_status', '=', '2'];
        $paid = $model->field($field)->where($where)->select();
        $this->assign('paid', $paid);
        foreach ($paid as $row) {
            $paymentList[$row['pay_type']] = $paymentList[$row['pay_type']] - $row['pay_item_price'];
        }
        $this->assign('unpaid', $paymentList);

        ### 获取复核人,手签不显示
        /**
        $where = [];
        $where[] = ['company_id', '=', $order->company_id];
        $where[] = ['timing', '=', 'payment'];
        $audit = Audit::where($where)->find();
        $auditRule = json_decode($audit['content'], true);
        $ids = $auditRule['hceo'];
        $hceos = [];
        foreach ($ids as $key=>$val) {
            $cuser = User::getUser($val);
            $hceos[] = $cuser['realname'];
        }
        $this->assign('hceos', implode(',', $hceos));
        **/

        $where = [];
        $where[] = ['order_id', '=', $payment->order_id];
        $where[] = ['item', '=', 'payment'];
        $where[] = ['item_id', '=', $payment->id];
        $print = OrderPrint::where($where)->find();
        $this->assign('print', $print);

        return $this->fetch();
    }

    public function doPrint()
    {
        $param = $this->request->param();
        $data = [];
        $data['user_id'] = $this->user['id'];
        $data['order_id'] = $param['order_id'];
        $data['item'] = $param['item'];
        $data['item_id'] = $param['item_id'];
        $orderPrint = new OrderPrint();
        $result = $orderPrint->save($data);
        echo $orderPrint->getLastSql();
        if($result && isset($orderPrint->id)) {
            $arr = [
                'code'  => '200',
                'msg'   => '打印成功'
            ];
        } else {
            $arr = [
                'code'  => '400',
                'msg'   => '打印失败'
            ];
        }

        return json($arr);
    }

    public function history()
    {
        $param = $this->request->param();
        $where[] = ['order_id', '=', $param['order_id']];
        $where[] = ['item', '=', 'payment'];
        $where[] = ['item_id', '=', $param['item_id']];
        $list = OrderPrint::where($where)->order('id desc')->select();
        $this->assign('list', $list);

        $users = User::getUsers(false);
        $this->assign('users', $users);
        return $this->fetch();
    }
}