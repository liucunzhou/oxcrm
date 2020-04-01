<?php

namespace app\common\command;

use app\common\model\OrderBanquet;
use app\common\model\OrderWedding;
use app\index\controller\Count;
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
            ->addOption('action', null, Option::VALUE_REQUIRED, '要执行的动作');
        // 设置参数

    }

    protected function execute(Input $input, Output $output)
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

            $data = [];
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
                $result = $banquetOrder->save($banquet);
                if ($result) {
                    // echo $row[5]."一站式写入成功\n";
                } else {
                    echo $banquetOrder->getLastSql() . "\n";
                    echo $row[5] . "婚宴写入失败\n";
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
                if ($result) {
                    // echo $row[5]."婚宴写入成功\n";
                } else {
                    echo $row[5] . "婚宴写入失败\n";
                }
            }
        }
    }
}
