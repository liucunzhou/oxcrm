<?php

namespace app\common\command;

use app\common\model\Brand;
use app\common\model\Hotel;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\MemberRelation;
use app\common\model\Mobile;
use app\common\model\MobileRelation;
use app\common\model\OrderBanquet;
use app\common\model\OrderBanquetPayment;
use app\common\model\OrderBanquetReceivables;
use app\common\model\OrderWedding;
use app\common\model\OrderWeddingPayment;
use app\common\model\OrderWeddingReceivables;
use app\common\model\Store;
use app\common\model\User;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Option;

class order extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('Order')
            ->addOption('action', null, Option::VALUE_REQUIRED, '要执行的动作')
            ->addOption('company_id', null, Option::VALUE_REQUIRED, '要执行的动作');
        // 设置参数
    }

    protected function execute(Input $input, Output $output)
    {
        $action = '';
        if ($input->hasOption("action")) {
            $action = $input->getOption("action");
        } else {
            $output->writeln("请输入要执行的操作");
            return false;
        }

        if ($input->hasOption("action")) {
            $action = $input->getOption("action");
        } else {
            $output->writeln("请输入要执行的操作");
            return false;
        }


        switch ($action) {
            case 'initMGN':
                $this->initMGN();
                break;

            case 'initHs':
                $this->initHs();
                break;

            case 'initOrder':
                $this->initOrder();
                break;

            case 'initStoreId':
                $this->initStoreId();
                break;

            case 'initStore':
                $this->initStore();
                break;

            case 'initSaleId':
                $this->initSaleId();
                break;

            case 'initMemberId':
                $this->initMemberId();
                break;

            case 'initMemberByRelation':
                $this->initMemberByRelation();
                break;

            case 'initCsvToTable':
                $this->initCsvToTable();
                break;

            case 'initComplete':
                $this->isComplete();
                break;

            case 'export':
                $this->export();
                break;
        }
    }

    public function isComplete()
    {
        $handle = fopen('./update.txt', 'r');

        $rows = [];
        $update = '';
        while (!feof($handle)) {
            $line = fgets($handle);
            preg_match('/`id` = \d+/', $line, $matches);
            $update .= "update tk_order set complete=100 where {$matches[0]};\n";
        }

        file_put_contents('./update.sql', $update);
        fclose($handle);
    }


    // 初始化红丝订单
    public function initMGN()
    {

        // 指令输出
        if (!$fp = fopen('./mangena.csv', 'r')) {
            return false;
        }

        while (!feof($fp)) {
            $row = fgetcsv($fp);
            foreach ($row as &$val) {
                $val = mb_convert_encoding($val, 'UTF-8', 'GBK');
            }

            if ($row[0] == '合同编号') continue;
            // 订单类型
            $data = [];
            $data['news_type'] = 2;
            $data['brand_text'] = $row[9];
            !empty($row[0]) && $data['contract_no'] = $row[0];
            !empty($row[6]) && $data['sign_date'] = strtotime($row[6]);
            !empty($row[7]) && $data['event_date'] = strtotime($row[7]);
            !empty($row[43]) && $data['source_text'] = $row[43];
            !empty($row[44]) && $data['source_detail'] = $row[44];
            !empty($row[5]) && $data['point'] = $row[5];
            !empty($row[46]) && $data['source_fee'] = $row[46];
            !empty($row[47]) && $data['person_source_fee'] = $row[47];
            !empty($row[50]) && $data['recommend_salesman'] = $row[50];
            !empty($row[16]) && $data['score'] = $row[16];
            !empty($row[1]) && $data['hotel_text'] = $row[1];
            !empty($row[12]) && $data['bridegroom'] = $row[12];
            !empty($row[13]) && $data['bride'] = $row[13];
            !empty($row[15]) && $data['bridegroom_mobile'] = $row[14];
            !empty($row[16]) && $data['bride_mobile'] = $row[15];
            !empty($row[2]) && $data['banquet_hall_name'] = $row[2];
            !empty($row[25]) && $data['contract_totals'] = $row[25];
            !empty($row[34]) && $data['totals'] = $row[34];
            !empty($row[27]) && $data['earnest_money'] = $row[27];
            !empty($row[29]) && $data['middle_money'] = $row[29];
            !empty($row[31]) && $data['tail_money'] = $row[31];
            !empty($row[17]) && $data['prepay_money'] = $row[17]; // 意向金
            !empty($row[31]) && $data['pay_hotel_fee'] = $row[31]; // 付酒店费用
            // !empty($row[33]) && $data['income_wedding_celebration_admission_fee'] = $row[33]; // 收婚庆进场费
            // !empty($row[34]) && $data['pay_hotel_admission_fee'] = $row[34]; // 付酒店进场费
            !empty($row[5]) && $data['sale'] = $row[5]; // 签单销售
            !empty($row[52]) && $data['sale_commission'] = $row[52]; // 销售提成
            !empty($row[26]) && $data['remark'] = $row[26]; // 订单备注
            !empty($row[35]) && $data['hotel_totals'] = $row[35]; // 酒店结算
            !empty($row[6]) && $data['create_time'] = strtotime($row[6]);
            $data['company_id'] = 24;
            $order = new \app\common\model\Order();
            $result = $order->save($data);
            echo "\n";
            if (!$result) {
                echo "失败\n";
                continue;
            } else {
                echo "写入成功\n";
            }
            $orderId = $order->id;
            $banquet = [];
            $banquet['order_id'] = $orderId;
            !empty($row[17]) && $banquet['table_amount'] = $row[17];
            !empty($row[18]) && $banquet['table_price'] = $row[18];
            !empty($row[20]) && $banquet['wine_fee'] = $row[20];
            !empty($row[19]) && $banquet['service_fee'] = $row[19];
            !empty($row[24]) && $banquet['banquet_discount'] = $row[24];
            !empty($row[22]) && $banquet['banquet_ritual_hall'] = $row[22];
            !empty($row[6]) && $banquet['create_time'] = strtotime($row[6]);
            $banquetOrder = new OrderBanquet();
            $result = $banquetOrder->insert($banquet);
            if ($result) {
                // echo $row[5]."一站式写入成功\n";
            } else {
                echo $banquetOrder->getLastSql() . "\n";
                echo $row[5] . "婚宴写入失败\n";
            }

            // 写入婚宴收款信息的定金收款
            if ($row[27]) {
                $banquetReceivables = new OrderBanquetReceivables();
                $receivable = [];
                $receivable['order_id'] = $orderId;
                $receivable['banquet_receivable_no'] = $row[28];
                $receivable['banquet_income_type'] = 1;
                $receivable['banquet_income_item_price'] = $row[27];
                $index = strpos($row[28],'-');
                $date = substr($row[28], 0, $index);
                $receivable['banquet_income_date'] = strtotime($date);
                $banquetReceivables->save($receivable);
            }

            // 婚宴收款中款
            if ($row[29]) {
                $banquetReceivables = new OrderBanquetReceivables();
                $receivable = [];
                $receivable['order_id'] = $orderId;
                $receivable['banquet_receivable_no'] = $row[30];
                $receivable['banquet_income_type'] = 2;
                $receivable['banquet_income_item_price'] = $row[29];
                $index = strpos($row[30],'-');
                $date = substr($row[30], 0, $index);
                $receivable['banquet_income_date'] = strtotime($date);
                $banquetReceivables->save($receivable);
            }

            // 婚宴收款尾款
            if ($row[31]) {
                $banquetReceivables = new OrderBanquetReceivables();
                $receivable = [];
                $receivable['order_id'] = $orderId;
                $receivable['banquet_receivable_no'] = $row[32];
                $receivable['banquet_income_type'] = 3;
                $receivable['banquet_income_item_price'] = $row[31];
                $index = strpos($row[32],'-');
                $date = substr($row[32], 0, $index);
                $receivable['banquet_income_date'] = strtotime($date);
                $banquetReceivables->save($receivable);
            }

            // 婚宴付款定金
            if ($row[36]) {
                $banquetPayment = new OrderBanquetPayment();
                $payment = [];
                $payment['order_id'] = $orderId;
                $payment['banquet_payment_no'] = $row[37];
                $payment['banquet_pay_type'] = 1;
                $payment['banquet_pay_item_price'] = $row[36];
                $index = strpos($row[37],'-');
                $date = substr($row[37], 0, $index);
                $payment['banquet_apply_pay_date'] = strtotime($date);
                $banquetPayment->save($payment);
            }

            // 婚宴付款中款
            if ($row[38]) {
                $banquetPayment = new OrderBanquetPayment();
                $payment = [];
                $payment['order_id'] = $orderId;
                $payment['banquet_payment_no'] = $row[39];
                $payment['banquet_pay_type'] = 2;
                $payment['banquet_pay_item_price'] = $row[38];
                $index = strpos($row[39],'-');
                $date = substr($row[39], 0, $index);
                $payment['banquet_apply_pay_date'] = strtotime($date);
                $banquetPayment->save($payment);
            }

            // 婚宴收款尾款
            if ($row[40]) {
                $banquetPayment = new OrderBanquetPayment();
                $payment = [];
                $payment['order_id'] = $orderId;
                $payment['banquet_payment_no'] = $row[41];
                $payment['banquet_pay_type'] = 3;
                $payment['banquet_pay_item_price'] = $row[40];
                $index = strpos($row[41],'-');
                $date = substr($row[41], 0, $index);
                $payment['banquet_apply_pay_date'] = strtotime($date);
                $banquetPayment->save($payment);
            }

            $wedding = [];
            $wedding['order_id'] = $orderId;
            !empty($row[21]) && $wedding['wedding_total'] = $row[21];
            !empty($row[50]) && $wedding['sale'] = $row[50];
            !empty($row[23]) && $wedding['light'] = $row[23];
            !empty($row[6]) && $wedding['create_time'] = strtotime($row[6]);
            $weddingOrder = new OrderWedding();
            $result = $weddingOrder->save($wedding);
            // $weddingId = $weddingOrder->id;

            if ($result) {
                // echo $row[5]."一站式写入成功\n";
            } else {
                echo $weddingOrder->getLastSql() . "\n";
                // echo $row[5]."一站式写入失败\n";
            }
        }
    }

    // 初始化红丝订单
    public function initHs()
    {

        // 指令输出
        if (!$fp = fopen('./hongsi.csv', 'r')) {
            return false;
        }

        while (!feof($fp)) {
            $row = fgetcsv($fp);
            foreach ($row as &$val) {
                $val = mb_convert_encoding($val, 'UTF-8', 'GBK');
            }

            if ($row[0] == '合同编号') continue;
            // 订单类型
            $data = [];
            $data['news_type'] = 2;
            $data['brand_text'] = $row[2];
            !empty($row[0]) && $data['contract_no'] = $row[0];
            !empty($row[1]) && $data['sign_date'] = strtotime($row[1]);
            !empty($row[9]) && $data['event_date'] = strtotime($row[9]);
            !empty($row[3]) && $data['source_text'] = $row[3];
            !empty($row[4]) && $data['source_detail'] = $row[4];
            !empty($row[5]) && $data['point'] = $row[5];
            !empty($row[6]) && $data['source_fee'] = $row[6];
            !empty($row[7]) && $data['recommend_salesman'] = $row[7];
            !empty($row[8]) && $data['score'] = $row[8];
            !empty($row[10]) && $data['hotel_text'] = $row[10];
            !empty($row[13]) && $data['bridegroom'] = $row[13];
            !empty($row[14]) && $data['bride'] = $row[14];
            !empty($row[15]) && $data['bridegroom_mobile'] = $row[15];
            !empty($row[16]) && $data['bride_mobile'] = $row[16];
            !empty($row[12]) && $data['banquet_hall_name'] = $row[12];
            !empty($row[19]) && $data['contract_totals'] = $row[19];
            !empty($row[24]) && $data['totals'] = $row[24];
            !empty($row[21]) && $data['earnest_money'] = $row[21];
            !empty($row[22]) && $data['middle_money'] = $row[22];
            !empty($row[23]) && $data['tail_money'] = $row[23];
            !empty($row[17]) && $data['prepay_money'] = $row[17]; // 意向金
            !empty($row[31]) && $data['pay_hotel_fee'] = $row[31]; // 付酒店费用
            // !empty($row[33]) && $data['income_wedding_celebration_admission_fee'] = $row[33]; // 收婚庆进场费
            // !empty($row[34]) && $data['pay_hotel_admission_fee'] = $row[34]; // 付酒店进场费
            !empty($row[51]) && $data['sale'] = $row[51]; // 签单销售
            !empty($row[52]) && $data['sale_commission'] = $row[52]; // 销售提成
            !empty($row[58]) && $data['remark'] = $row[58]; // 订单备注
            !empty($row[1]) && $data['create_time'] = strtotime($row[1]);
            $data['company_id'] = 26;
            $order = new \app\common\model\Order();
            $result = $order->save($data);
            echo "\n";
            if (!$result) {
                echo "失败\n";
                continue;
            } else {
                echo "写入成功\n";
            }
            $orderId = $order->id;
            $banquet = [];
            $banquet['order_id'] = $orderId;
            !empty($row[31]) && $banquet['table_amount'] = $row[31];
            !empty($row[32]) && $banquet['table_price'] = $row[32];
            !empty($row[33]) && $banquet['wine_fee'] = $row[33];
            // !empty($row[17]) && $banquet['service_fee'] = $row[17];
            !empty($row[1]) && $banquet['create_time'] = strtotime($row[1]);
            $banquetOrder = new OrderBanquet();
            $result = $banquetOrder->insert($banquet);
            if ($result) {
                // echo $row[5]."一站式写入成功\n";
            } else {
                echo $banquetOrder->getLastSql() . "\n";
                echo $row[5] . "婚宴写入失败\n";
            }

            // 写入婚宴收款信息的定金收款
            if ($row[25]==0) {
                $banquetReceivables = new OrderBanquetReceivables();
                $receivable = [];
                $receivable['order_id'] = $orderId;
                $receivable['banquet_receivable_no'] = $row[26];
                $receivable['banquet_income_type'] = 1;
                $receivable['banquet_income_item_price'] = $row[21];
                $receivable['banquet_income_date'] = $row[1];
                $banquetReceivables->insert($receivable);
            }

            // 婚宴收款中款
            if ($row[27]==0) {
                $banquetReceivables = new OrderBanquetReceivables();
                $receivable = [];
                $receivable['order_id'] = $orderId;
                $receivable['banquet_receivable_no'] = $row[28];
                $receivable['banquet_income_type'] = 2;
                $receivable['banquet_income_item_price'] = $row[22];
                $receivable['banquet_income_date'] = $row[1];
                $banquetReceivables->save($receivable);
            }

            // 婚宴收款尾款
            if ($row[29]) {
                $banquetReceivables = new OrderBanquetReceivables();
                $receivable = [];
                $receivable['order_id'] = $orderId;
                $receivable['banquet_receivable_no'] = $row[30];
                $receivable['banquet_income_type'] = 3;
                $receivable['banquet_income_item_price'] = $row[23];
                $receivable['banquet_income_date'] = $row[1];
                $banquetReceivables->save($receivable);
            }

            // 婚宴付款定金
            if ($row[36]) {
                $banquetPayment = new OrderBanquetPayment();
                $payment = [];
                $payment['order_id'] = $orderId;
                $payment['banquet_payment_no'] = $row[37];
                $payment['banquet_pay_type'] = 1;
                $payment['banquet_pay_item_price'] = $row[36];
                $payment['banquet_apply_pay_date'] = $row[1];
                $banquetPayment->save($payment);
            }

            // 婚宴付款中款
            if ($row[38]) {
                $banquetPayment = new OrderBanquetPayment();
                $payment = [];
                $payment['order_id'] = $orderId;
                $payment['banquet_payment_no'] = $row[39];
                $payment['banquet_pay_type'] = 2;
                $payment['banquet_pay_item_price'] = $row[38];
                $payment['banquet_apply_pay_date'] = $row[1];
                $banquetPayment->save($payment);
            }

            // 婚宴收款尾款
            if ($row[40]) {
                $banquetPayment = new OrderBanquetPayment();
                $payment = [];
                $payment['order_id'] = $orderId;
                $payment['banquet_payment_no'] = $row[41];
                $payment['banquet_pay_type'] = 3;
                $payment['banquet_pay_item_price'] = $row[40];
                $payment['banquet_apply_pay_date'] = $row[1];
                $banquetPayment->save($payment);
            }

            $wedding = [];
            $wedding['order_id'] = $orderId;
            !empty($row[20]) && $wedding['wedding_total'] = $row[20];
            !empty($row[50]) && $wedding['sale'] = $row[50];
            !empty($row[46]) && $wedding['car'] = $row[46];
            !empty($row[1]) && $wedding['create_time'] = strtotime($row[1]);
            $weddingOrder = new OrderWedding();
            $result = $weddingOrder->save($wedding);
            // $weddingId = $weddingOrder->id;

            if ($result) {
                // echo $row[5]."一站式写入成功\n";
            } else {
                echo $weddingOrder->getLastSql() . "\n";
                // echo $row[5]."一站式写入失败\n";
            }
        }
    }

    // 初始化誉思订单
    public function initOrder()
    {
        $types = [
            '婚宴' => 0,
            '婚庆' => 1,
            '一站式' => 2
        ];
        // 指令输出
        if (!$fp = fopen('./order25.csv', 'r')) {
            return false;
        }

        while (!feof($fp)) {
            $row = fgetcsv($fp);
            foreach ($row as &$val) {
                $val = mb_convert_encoding($val, 'UTF-8', 'GBK');
            }
            if ($row[0] == '合同编号') continue;
            // 订单类型
            $data = [];
            if (empty($row[12])) {
                $data['news_type'] = 2;
                $data['brand_text'] = '-1';
            } else if ($row[12] == 'LK') {
                // 婚宴
                $data['news_type'] = 0;
                $data['brand_text'] = 'LK';
            } else if ($row[12] == '红丝') {
                $data['news_type'] = 0;
                $data['brand_text'] = '红丝';
            } else if ($row[12] == '婚宴') {
                $data['news_type'] = 0;
            } else if ($row[12] == '一站式') {
                $data['news_type'] = 2;
            }
            !empty($row[0]) && $data['contract_no'] = $row[0];
            !empty($row[1]) && $data['sign_date'] = strtotime($row[1]);
            !empty($row[2]) && $data['event_date'] = strtotime($row[2]);
            !empty($row[3]) && $data['source_text'] = $row[3];
            !empty($row[4]) && $data['point'] = $row[4];
            !empty($row[5]) && $data['score'] = $row[5];
            !empty($row[6]) && $data['hotel_text'] = $row[6];
            !empty($row[7]) && $data['bridegroom'] = $row[7];
            !empty($row[8]) && $data['bride'] = $row[8];
            !empty($row[9]) && $data['bridegroom_mobile'] = $row[9];
            !empty($row[10]) && $data['bride_mobile'] = $row[10];
            !empty($row[11]) && $data['banquet_hall_name'] = $row[11];
            !empty($row[13]) && $data['totals'] = $row[13];
            !empty($row[21]) && $data['earnest_money'] = $row[21];
            !empty($row[25]) && $data['middle_money'] = $row[25];
            !empty($row[29]) && $data['tail_money'] = $row[29];
            !empty($row[19]) && $data['prepay_money'] = $row[19]; // 意向金
            !empty($row[31]) && $data['pay_hotel_fee'] = $row[31]; // 付酒店费用
            !empty($row[33]) && $data['income_wedding_celebration_admission_fee'] = $row[33]; // 收婚庆进场费
            !empty($row[34]) && $data['pay_hotel_admission_fee'] = $row[34]; // 付酒店进场费

            !empty($row[35]) && $data['source_fee'] = $row[35];
            !empty($row[37]) && $data['sale'] = $row[37]; // 签单销售
            !empty($row[38]) && $data['sale_commission'] = $row[38]; // 销售提成
            !empty($row[39]) && $data['remark'] = $row[39]; // 订单备注
            !empty($row[1]) && $data['create_time'] = strtotime($row[1]);
            $data['company_id'] = 25;
            $order = new \app\common\model\Order();
            $result = $order->save($data);
            echo $order->getLastSql();
            echo "\n";
            if (!$result) {
                // echo $order->getLastSql();
                // echo "\n";
            }
            $orderId = $order->id;
            if ($data['news_type'] == 2) {

                $banquet = [];
                $banquet['order_id'] = $orderId;
                !empty($row[14]) && $banquet['table_amount'] = $row[14];
                !empty($row[15]) && $banquet['table_price'] = $row[15];
                !empty($row[16]) && $banquet['wine_fee'] = $row[16];
                !empty($row[17]) && $banquet['service_fee'] = $row[17];
                !empty($row[1]) && $banquet['create_time'] = strtotime($row[1]);
                $banquetOrder = new OrderBanquet();
                $result = $banquetOrder->insert($banquet);
                if ($result) {
                    // echo $row[5]."一站式写入成功\n";
                } else {
                    echo $banquetOrder->getLastSql() . "\n";
                    echo $row[5] . "婚宴写入失败\n";
                }

                // 写入婚宴收款信息的定金收款
                if ($row[21]) {
                    $banquetReceivables = new OrderBanquetReceivables();
                    $receivable = [];
                    $receivable['order_id'] = $orderId;
                    $receivable['banquet_receivable_no'] = $row[22];
                    $receivable['banquet_income_type'] = 1;
                    $receivable['banquet_income_item_price'] = $row[21];
                    $receivable['banquet_income_date'] = $row[1];
                    $banquetReceivables->insert($receivable);
                }

                // 婚宴收款中款
                if ($row[25]) {
                    $banquetReceivables = new OrderBanquetReceivables();
                    $receivable = [];
                    $receivable['order_id'] = $orderId;
                    $receivable['banquet_receivable_no'] = $row[26];
                    $receivable['banquet_income_type'] = 2;
                    $receivable['banquet_income_item_price'] = $row[25];
                    $receivable['banquet_income_date'] = $row[1];
                    $banquetReceivables->save($receivable);
                }

                // 婚宴收款尾款
                if ($row[29]) {
                    $banquetReceivables = new OrderBanquetReceivables();
                    $receivable = [];
                    $receivable['order_id'] = $orderId;
                    $receivable['banquet_receivable_no'] = $row[30];
                    $receivable['banquet_income_type'] = 3;
                    $receivable['banquet_income_item_price'] = $row[29];
                    $receivable['banquet_income_date'] = $row[1];
                    $banquetReceivables->save($receivable);
                }

                // 婚宴付款定金
                if ($row[23]) {
                    $banquetPayment = new OrderBanquetPayment();
                    $payment = [];
                    $payment['order_id'] = $orderId;
                    $payment['banquet_payment_no'] = $row[24];
                    $payment['banquet_pay_type'] = 1;
                    $payment['banquet_pay_item_price'] = $row[23];
                    $payment['banquet_apply_pay_date'] = $row[1];
                    $banquetPayment->save($payment);
                }

                // 婚宴付款中款
                if ($row[27]) {
                    $banquetPayment = new OrderBanquetPayment();
                    $payment = [];
                    $payment['order_id'] = $orderId;
                    $payment['banquet_payment_no'] = $row[28];
                    $payment['banquet_pay_type'] = 2;
                    $payment['banquet_pay_item_price'] = $row[27];
                    $payment['banquet_apply_pay_date'] = $row[1];
                    $banquetPayment->save($payment);
                }

                // 婚宴收款尾款
                if ($row[31]) {
                    $banquetPayment = new OrderBanquetPayment();
                    $payment = [];
                    $payment['order_id'] = $orderId;
                    $payment['banquet_payment_no'] = $row[32];
                    $payment['banquet_pay_type'] = 3;
                    $payment['banquet_pay_item_price'] = $row[31];
                    $payment['banquet_apply_pay_date'] = $row[1];
                    $banquetPayment->save($payment);
                }

                $wedding = [];
                $wedding['order_id'] = $orderId;
                !empty($row[36]) && $wedding['wedding_total'] = $row[36];
                !empty($row[1]) && $wedding['create_time'] = strtotime($row[1]);
                $weddingOrder = new OrderWedding();
                $result = $weddingOrder->save($wedding);
                // $weddingId = $weddingOrder->id;

                if ($result) {
                    // echo $row[5]."一站式写入成功\n";
                } else {
                    echo $weddingOrder->getLastSql() . "\n";
                    // echo $row[5]."一站式写入失败\n";
                }

            } else {

                $banquet = [];
                $banquet['order_id'] = $orderId;
                !empty($row[14]) && $banquet['table_amount'] = $row[14];
                !empty($row[15]) && $banquet['table_price'] = $row[15];
                !empty($row[16]) && $banquet['wine_fee'] = $row[16];
                !empty($row[17]) && $banquet['service_fee'] = $row[17];
                !empty($row[1]) && $banquet['create_time'] = strtotime($row[1]);
                $banquetOrder = new OrderBanquet();
                $result = $banquetOrder->save($banquet);

                // 写入婚宴收款信息的定金收款
                if ($row[21]) {
                    $banquetReceivables = new OrderBanquetReceivables();
                    $receivable = [];
                    $receivable['order_id'] = $orderId;
                    $receivable['banquet_receivable_no'] = $row[22];
                    $receivable['banquet_income_type'] = 1;
                    $receivable['banquet_income_item_price'] = $row[21];
                    $receivable['banquet_income_date'] = $row[1];
                    $banquetReceivables->save($receivable);
                }

                // 婚宴收款中款
                if ($row[25]) {
                    $banquetReceivables = new OrderBanquetReceivables();
                    $receivable = [];
                    $receivable['order_id'] = $orderId;
                    $receivable['banquet_receivable_no'] = $row[26];
                    $receivable['banquet_income_type'] = 2;
                    $receivable['banquet_income_item_price'] = $row[25];
                    $receivable['banquet_income_date'] = $row[1];
                    $banquetReceivables->save($receivable);
                }

                // 婚宴收款尾款
                if ($row[29]) {
                    $banquetReceivables = new OrderBanquetReceivables();
                    $receivable = [];
                    $receivable['order_id'] = $orderId;
                    $receivable['banquet_receivable_no'] = $row[30];
                    $receivable['banquet_income_type'] = 3;
                    $receivable['banquet_income_item_price'] = $row[29];
                    $receivable['banquet_income_date'] = $row[1];
                    $banquetReceivables->save($receivable);
                }

                // 婚宴付款定金
                if ($row[23]) {
                    $banquetPayment = new OrderBanquetPayment();
                    $payment = [];
                    $payment['order_id'] = $orderId;
                    $payment['banquet_payment_no'] = $row[24];
                    $payment['banquet_pay_type'] = 1;
                    $payment['banquet_pay_item_price'] = $row[23];
                    $payment['banquet_apply_pay_date'] = $row[1];
                    $banquetPayment->save($payment);
                }

                // 婚宴付款中款
                if ($row[27]) {
                    $banquetPayment = new OrderBanquetPayment();
                    $payment = [];
                    $payment['order_id'] = $orderId;
                    $payment['banquet_payment_no'] = $row[28];
                    $payment['banquet_pay_type'] = 2;
                    $payment['banquet_pay_item_price'] = $row[27];
                    $payment['banquet_apply_pay_date'] = $row[1];
                    $banquetPayment->save($payment);
                }

                // 婚宴收款尾款
                if ($row[31]) {
                    $banquetPayment = new OrderBanquetPayment();
                    $payment = [];
                    $payment['order_id'] = $orderId;
                    $payment['banquet_payment_no'] = $row[32];
                    $payment['banquet_pay_type'] = 3;
                    $payment['banquet_pay_item_price'] = $row[31];
                    $payment['banquet_apply_pay_date'] = $row[1];
                    $banquetPayment->save($payment);
                }

                if ($result) {
                    // echo $row[5]."婚宴写入成功\n";
                } else {
                    echo $row[5] . "婚宴写入失败\n";
                }
            }
        }
    }

    public function initStoreId()
    {
        $orderModel = new \app\common\model\Order();
        $order = $orderModel->select();
        foreach ($order as $row) {
            $where = [];
            $where['title'] = $row->hotel_text;
            $store = Store::where($where)->find();
            if (!empty($store)) {
                $data = [];
                $data['hotel_id'] = $store->id;
                $result = $row->save($data);
                // echo $row->getLastSql();
                // echo "\n";
                if (!$result) {
                    echo $row->id . "订单匹配失败\n";
                }
            } else {
                file_put_contents('./hotels.txt', $row->hotel_text . "\n", FILE_APPEND);
                echo $row->id . "酒店未匹配\n";
            }
        }
    }

    public function initSaleId()
    {
        $orderModel = new \app\common\model\Order();
        $order = $orderModel->select();
        foreach ($order as $row) {
            $realname = trim($row->sale);
            $where = [];
            // $where[] = ['role_id', '>', 0];
            $where[] = ['realname', 'like', "%{$realname}%"];
            // $where[] = ['is_valid', '=', 1];
            $user = User::where($where)->find();
            if (!empty($user)) {
                $data = [];
                $data['salesman'] = $user->id;
                $result = $row->save($data);
                // echo $row->getLastSql();
                // echo "\n";
                if (!$result) {
                    echo $row->sale . "订单匹配失败\n";
                }
            } else {
                file_put_contents('./sale.txt', $row->id . ":::" . $row->sale . "\n", FILE_APPEND);
                echo $row->sale . "未匹配\n";
            }
        }
    }

    // 初始化Store表
    public function initStore()
    {
        $citys = [
            '0' => 0,
            '上海市' => '802',
            '广州市' => '1965',
            '杭州市' => '934',
            '苏州市' => '861'
        ];

        $areas = [
            '松江区' => '816',
            '江干区' => '937',
            '浦东新区' => '814',
            '海珠区' => '1968',
            '滨江区' => '940',
            '番禺区' => '1972',
            '白云区' => '1970',
            '相城区' => '864',
            '萧山区' => '941',
            '虎丘区' => '862',
            '虹口区' => '809',
            '西湖区' => '939',
            '越秀区' => '1967',
            '金山区' => '815',
            '长宁区' => '805',
            '闵行区' => '811',
            '闸北区' => '808',
            '青浦区' => '817',
            '静安区' => '806',
            '黄埔区' => '1971',
            '黄浦区' => '803',
        ];

        $hotels = Hotel::select();
        foreach ($hotels as $hotel) {
            $store = new Store();
            $data = [];
            $data['title'] = $hotel->ho_name;
            $data['wechat_id'] = $hotel->id;
            // $data['sttore_no'] = '';
            $data['brand_id'] = 0;
            $data['admin_id'] = 1;
            $data['is_entire'] = $hotel->ho_money_max;
            $data['titlepic'] = $hotel->ho_coverurl;
            $data['images'] = $hotel->ho_carouselurll . ':::' . $hotel->ho_carouselurle . ':::' . $hotel->ho_carouselurls . ':::' . $hotel->ho_carouselurlsh;
            $data['type'] = $hotel->ho_type;
            $data['characteristics'] = $hotel->ho_characteristics;
            $data['province_id'] = 0;
            $data['city_id'] = isset($citys[$hotel->ho_city]) ? $citys[$hotel->ho_city] : 0;
            $data['area_id'] = isset($areas[$hotel->ho_area]) ? $areas[$hotel->ho_area] : 0;
            $data['min_price'] = $hotel->ho_money_min;
            $data['star'] = $hotel->ho_drill;
            $data['address'] = $hotel->ho_adddres;
            $rs = $store->save($data);
            if ($rs) {
                echo $hotel->ho_name . "初始化成功\n";
            } else {
                echo $hotel->ho_name . "初始化失败\n";
            }
        }
    }

    public function initMemberId()
    {
        file_put_contents('./member-no.txt', '');
        $orderModel = new \app\common\model\Order();
        $order = $orderModel->select();
        foreach ($order as $row) {
            $where1 = [];
            $where2 = [];
            $where1[] = ['user_id', '=', $row->salesman];
            if (empty($row->bridegroom_mobile) && empty($row->bride_mobile)) {
                file_put_contents('./member-no.txt', $row->sign_date . ":::" . $row->bridegroom_mobile . "\n", FILE_APPEND);
                continue;
            } else if (!empty($row->bridegroom_mobile) && empty($row->bride_mobile)) {
                $where1[] = ['mobile', '=', $row->bridegroom_mobile];
                $where2[] = ['mobile1', '=', $row->bridegroom_mobile];
            } else if (empty($row->bridegroom_mobile) && !empty($row->bride_mobile)) {
                $where1[] = ['mobile', '=', $row->bride_mobile];
                $where2[] = ['mobile1', '=', $row->bride_mobile];
            } else {
                $where1[] = ['mobile', 'in', [$row->bridegroom_mobile, $row->bride_mobile]];
                $where2[] = ['mobile1', 'in', [$row->bridegroom_mobile, $row->bride_mobile]];
            }

            $mobile = MemberAllocate::where($where1)->whereOr($where2)->find();
            if (!empty($mobile)) {
                $data = [];
                $data['member_id'] = $mobile->member_id;
                $data['member_allocate_id'] = $mobile->id;
                $data['salesman'] = $mobile->user_id;
                $result = $row->save($data);
                // echo $row->getLastSql();
                // echo "\n";
                $result = 1;
                if (!$result) {
                    echo $row->sale . "订单匹配失败\n";
                }
            } else {
                file_put_contents('./member.txt', $row->bride_mobile . ":::" . $row->bridegroom_mobile . ":::".$row->salesman.":::".$row->id."\n", FILE_APPEND);
                echo $row->sale . "未匹配\n";
            }
        }
    }

    public function initMemberByRelation()
    {
        file_put_contents('./member-end.txt', '');
        $file = file_get_contents("./member.txt");
        $rows = explode("\n", $file);

        foreach ($rows as $row) {
            $arr = explode(":::", $row);
            echo count($arr);
            if(count($arr) < 3) continue;
            print_r($arr);
            if (!empty($arr[0]) && empty($arr[1])) {
                $where = [];
                $where[] = ['mobiles', 'like', '%{$arr[0]}%'];
                $relation = MobileRelation::where($where)->find();
                echo "\n";
                echo MobileRelation::getLastSql();
                echo "\n";
                if(empty($relation)) {
                    file_put_contents('./member-end.txt', $arr[0] . ":::" . $arr[1] . ":::".$arr[2]."\n", FILE_APPEND);
                    echo $arr[2]."失败\n";
                    continue;
                }

                $mobiles = explode(',',$relation->mobiles);
                $where1 = [];
                $where1[] = ['mobile', 'in', $mobiles];
                $where2 = [];
                $where2[] = ['mobile1', 'in', $mobiles];
                $allocate = MemberAllocate::where('user_id', '=', $arr[2])->where($where1)->whereOr($where2)->find();
                echo MemberAllocate::getLastSql();
                echo "\n";
                if($allocate) {
                    echo $allocate->id."成功\n";

                } else {
                    file_put_contents('./member-end.txt', $arr[0] . ":::" . $arr[1] . ":::".$arr[2]."\n", FILE_APPEND);
                    echo $arr[2]."失败\n";
                }

            } else if (empty($arr[0]) && !empty($arr[1])) {
                $where = [];
                $where[] = ['mobiles', 'like', '%{$arr[1]}%'];
                $relation = MobileRelation::where($where)->find();
                if(empty($relation)) {
                    file_put_contents('./member-end.txt', $arr[0] . ":::" . $arr[1] . ":::".$arr[2]."\n", FILE_APPEND);
                    echo $arr[2]."失败\n";
                    continue;
                }

                $mobiles = explode(',',$relation->mobiles);
                $where1 = [];
                $where1[] = ['mobile', 'in', $mobiles];
                $where2 = [];
                $where2[] = ['mobile1', 'in', $mobiles];
                $allocate = MemberAllocate::where('user_id', '=', $arr[2])->where($where1)->whereOr($where2)->find();
                echo MemberAllocate::getLastSql();
                echo "\n";
                if($allocate) {
                    echo $allocate->id."成功\n";
                } else {
                    file_put_contents('./member-end.txt', $arr[0] . ":::" . $arr[1] . ":::".$arr[2]."\n", FILE_APPEND);
                    echo $arr[2]."失败\n";
                }
            } else {
                $where = [];
                $where[] = ['mobiles', 'like', '%{$arr[0]}%'];
                $relation = MobileRelation::where($where)->find();
                if(!empty($relation)) {
                    $mobiles = explode(',',$relation->mobiles);
                    $where1 = [];
                    $where1[] = ['mobile', 'in', $mobiles];
                    $where2 = [];
                    $where2[] = ['mobile1', 'in', $mobiles];
                    $allocate1 = MemberAllocate::where('user_id', '=', $arr[2])->where($where1)->whereOr($where2)->find();
                    echo MemberAllocate::getLastSql();
                } else {
                    $allocate1 = [];
                }

                $where = [];
                $where[] = ['mobiles', 'like', '%{$arr[1]}%'];
                $relation = MobileRelation::where($where)->find();
                if(!empty($relation)) {
                    $mobiles = explode(',',$relation->mobiles);
                    $where1 = [];
                    $where1[] = ['mobile', 'in', $mobiles];
                    $where2 = [];
                    $where2[] = ['mobile1', 'in', $mobiles];
                    $allocate2 = MemberAllocate::where('user_id', '=', $arr[2])->where($where1)->whereOr($where2)->find();
                    echo MemberAllocate::getLastSql();
                } else {
                    $allocate2 = [];
                }

                if(!empty($allocate1) && empty($allocate2)) {
                    $allocate = $allocate1;
                } else if (empty($allocate1) && !empty($allocate2)) {
                    $allocate = $allocate2;
                } else if (!empty($allocate1) && !empty($allocate2)) {
                    $allocate = $allocate1;
                } else {
                    $allocate = false;
                }

                if($allocate) {
                    echo $allocate->id."成功\n";

                } else {
                    file_put_contents('./member-end.txt', $arr[0] . ":::" . $arr[1] . ":::".$arr[2]."\n", FILE_APPEND);
                    echo $arr[2]."失败\n";
                }
            }
        }
    }

    ### 导出
    public function export()
    {
        $config = config();
        $config = $config['crm'];
        $newsTypes = $config['news_type_list'];
        $brands = Brand::getBrands();
        $hotels = Store::getStoreList();
        $statuses = [0=>'待完成',99=>'意向金',100=>'已完成',101=>'退单'];
        $paymentTypes = [1=>'定金', 2=>'中款', 3=>'尾款', 4=>'意向金', 5=>'二销'];

        $file = './shell/order-export.csv';
        $fp = fopen($file, 'w+');
        $order = new \app\common\model\Order();
        $orderList = $order->select();
        $header = [
            '签单销售','签单类型','婚庆所属公司','签单日期', '婚期','酒店','厅',
            '新郎','手机号','新娘','手机号'
            ,'餐标','桌数',
            '合同总额','定金收款日期','定金收款金额','中款收款日期','中款收款金额','尾款收款日期','尾款收款金额'
            ,'收款信息中的定金','收款信息中的中款','收款信息中的尾款','付款信息中的定金','付款信息中的中款','付款信息中的尾款'
            ,'订单状态','所有备注'
        ];
        $users = User::getUsers();
        fputcsv($fp, $header);
        foreach ($orderList as $key=>$val) {
            $row = $val->getData();
            $data = [];
            if(!empty($row['sale'])) {
                $data[] = $row['sale'];
            } else if (!empty($row['user_id'])) {
                $data[] = $users[$row['user_id']]['realname'];
            } else {
                $data[] = '-';
            }
            $data[] = isset($newsTypes[$row['news_type']]) ? $newsTypes[$row['news_type']] : '-';
            $data[] = isset($brands[$row['company_id']]) ? $brands[$row['company_id']]['title'] : '-';
            $data[] = date('Y-m-d', $row['sign_date']);
            $data[] = date('Y-m-d', $row['event_date']);
            if (!empty($row['hotel_text'])) {
                $data[] = $row['hotel_text'];
            } else if (!empty($row['hotel_id'])) {
                $data[] = $hotels[$row['hotel_id']]['title'];
            } else {
                $data[] = '-';
            }
            $data[] = !empty($row['banquet_hall_name']) ? $row['banquet_hall_name'] : '-';
            ### 新人信息
            $data[] = !empty($row['bridegroom']) ? $row['bridegroom'] : '-';
            $data[] = !empty($row['bridegroom_mobile']) ? $row['bridegroom_mobile'] : '-';
            $data[] = !empty($row['bride']) ? $row['bride'] : '-';
            $data[] = !empty($row['bride_mobile']) ? $row['bride_mobile'] : '-';
            ### 餐标、桌数
            $banquet = OrderBanquet::where('order_id', '=', $row['id'])->find();
            if(!empty($banquet)) {
                $data[] = !empty($banquet['table_price']) ? $banquet['table_price'] : '-';
                $data[] = !empty($banquet['table_amount']) ? $banquet['table_amount'] : '-';
            } else {
                $data[] = '-';
                $data[] = '-';
            }

            ### 合同信息
            $data[] = !empty($row['totals']) ? $row['totals'] : '-';
            $data[] = !empty($row['earnest_money_date']) ?  date('Y-m-d',$row['earnest_money']) : '-';
            $data[] = !empty($row['earnest_money']) ? $row['earnest_money_date'] : '-';
            $data[] = !empty($row['middle_money_date']) ? date('Y-m-d', $row['middle_money_date']) : '-';
            $data[] = !empty($row['middle_money']) ? $row['middle_money'] : '-';
            $data[] = !empty($row['tail_money_date']) ? date('Y-m-d', $row['tail_money_date']) : '-';
            $data[] = !empty($row['tail_money']) ? $row['tail_money'] : '-';

            ### 收款信息
            $earnestIncome = 0;
            $middleIncome = 0;
            $tailIncome = 0;
            $banquetIncomeList = OrderBanquetReceivables::where('order_id', '=', $row['id'])->select();
            $remark = '';
            foreach ($banquetIncomeList as $value) {
                if ($value->banquet_income_type == 1) {
                    $earnestIncome = $earnestIncome + $value->banquet_income_item_price;
                } else if ($value->banquet_income_type == 2) {
                    $middleIncome = $middleIncome + $value->banquet_income_item_price;
                } else if ($value->banquet_income_type == 3) {
                    $tailIncome = $tailIncome + $value->banquet_income_item_price;
                }
                $remark .= $value['remark'];
            }
            $weddingIncomeList = OrderWeddingReceivables::where('order_id', '=', $row['id'])->select();
            foreach ($weddingIncomeList as $value) {
                if ($value->wedding_income_type == 1) {
                    $earnestIncome = $earnestIncome + $value->wedding_income_item_price;
                } else if ($value->wedding_income_type == 2) {
                    $middleIncome = $middleIncome + $value->wedding_income_item_price;
                } else if ($value->wedding_income_type == 3) {
                    $tailIncome = $tailIncome + $value->wedding_income_item_price;
                }
                $remark .= $value['remark'];
            }
            $data[] = $earnestIncome;
            $data[] = $middleIncome;
            $data[] = $tailIncome;
            ### 付款信息
            $earnestPayment = 0;
            $middlePayment = 0;
            $tailPayment = 0;
            $banquetPaymentList = OrderBanquetPayment::where('order_id', '=', $row['id'])->select();
            foreach ($banquetPaymentList as $value) {
                if ($value->banquet_pay_type == 1) {
                    $earnestPayment = $earnestPayment + $value->banquet_pay_item_price;
                } else if ($value->banquet_pay_type == 2) {
                    $middlePayment = $middlePayment + $value->banquet_pay_item_price;
                } else if ($value->banquet_pay_type == 3) {
                    $tailPayment = $tailPayment + $value->banquet_pay_item_price;
                }
                $remark .= $value['banquet_payment_remark'];
            }
            $weddingPaymentList = OrderWeddingPayment::where('order_id', '=', $row['id'])->select();
            foreach ($weddingPaymentList as $value) {
                if ($value->wedding_pay_type == 1) {
                    $earnestIncome = $earnestIncome + $value->wedding_pay_item_price;
                } else if ($value->wedding_pay_type == 2) {
                    $middlePayment = $middlePayment + $value->wedding_pay_item_price;
                } else if ($value->wedding_pay_type == 3) {
                    $tailPayment = $tailPayment + $value->wedding_pay_item_price;
                }
                $remark .= $value['wedding_payment_remark'];
            }
            $data[] = $earnestPayment;
            $data[] = $middlePayment;
            $data[] = $tailPayment;

            ### 订单状态以及备注
            $data[] = isset($statuses[$row['complete']]) ? $statuses[$row['complete']] : '-';
            $data[] = $row['remark'].$remark;
            fputcsv($fp, $data);
        }
        fclose($fp);
    }
}
