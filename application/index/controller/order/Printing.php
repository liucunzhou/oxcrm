<?php
namespace app\index\controller\order;

use app\common\model\Brand;
use app\common\model\OrderBanquetPayment;
use app\common\model\OrderWeddingPayment;
use app\wash\controller\Backend;

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
            $data['pay_to_company'] = $payment->wedding_pay_to_company;
            $data['pay_to_account'] = $payment->wedding_pay_to_account;
            $data['pay_to_bank'] = $payment->wedding_pay_to_bank;
        } else {
            $data['pay_to_company'] = $payment->banquet_pay_to_company;
            $data['pay_to_account'] = $payment->banquet_pay_to_account;
            $data['pay_to_bank'] = $payment->banquet_pay_to_bank;
        }
        $this->assign('data', $data);

        $companies = Brand::getBrands();
        $order = \app\common\model\Order::get($payment->order_id);
        $order['company'] = $companies[$order->company_id]['title'];
        $this->assign('order', $order);

        return $this->fetch();
    }
}