<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/9/17
 * Time: 6:48 PM
 */

namespace app\common\command;

use app\common\model\OrderConfirm;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Review extends Command
{
    protected function configure()
    {
        $this->setName("Review")
            ->addOption('action', null, Option::VALUE_REQUIRED, '要执行的动作')
            ->addOption('page', null, Option::VALUE_OPTIONAL, '分页');
    }

    protected function execute(Input $input, Output $output)
    {
        if ($input->hasOption("action")) {
            $action = $input->getOption("action");
        } else {
            $output->writeln("请输入要执行的操作");
            return false;
        }

        switch ($action) {
            case 'validate':
                $this->validate();
                break;
        }
    }

    public function validate()
    {
        $model = new OrderConfirm();

        $where = [];
        $where[] = ['confirm_intro', 'like', '%编辑%'];
        $confirmNos = $model->field('confirm_no')->where($where)->group('confirm_no')->select();
        foreach ($confirmNos as $row) {
            // echo $row['confirm_no'];
            // 倒序查找对应的审核
            $where = [];
            $where[] = ['confirm_no', '=', $row['confirm_no']];
            $confirm = OrderConfirm::where($where)->order('id desc')->find();

            $source = json_decode($confirm->source, true);
            // print_r($source['order']['item_check_status']);
            foreach ($source as $key=>$item) {
                if( $key == 'order') {
                    $where = [];
                    $where[] = ['id', '=', $source['order']['id']];
                    $order = \app\common\model\Order::where($where)->find();
                    if ($confirm->status == 0) {
                        echo "审核ID:".$confirm->id;
                        echo "\n";
                        echo "当前审核状态:" . $confirm->status;
                        echo "\n";
                        echo "订单审核状态" . $order->item_check_status;
                        echo "\n";
                    }
                } else if ($key == 'banquet') {
                    if (empty($source['banquet']['id'])) continue;
                    $where = [];
                    $where[] = ['id', '=', $source['banquet']['id']];
                    $model = \app\common\model\OrderBanquet::where($where)->find();
                    if ($confirm->status == 0) {
                        echo "审核ID:".$confirm->id;
                        echo "\n";
                        echo "当前审核状态:" . $confirm->status;
                        echo "\n";
                        echo "婚宴审核状态" . $model->item_check_status;
                        echo "\n";
                    }
                } else if($key == 'banquetIncome') {

                    if (empty($source['banquetIncome']['id'])) continue;
                    $where = [];
                    $where[] = ['id', '=', $source['banquetIncome']['id']];
                    $model = \app\common\model\OrderBanquetReceivables::where($where)->find();
                    if ($confirm->status == 0) {
                        echo "审核ID:".$confirm->id;
                        echo "\n";
                        echo "当前审核状态:" . $confirm->status;
                        echo "\n";
                        echo "婚宴收款审核状态" . $model->item_check_status;
                        echo "\n";
                    }

                } else if($key == 'banquetPayment') {

                    if (empty($source['banquetPayment']['id'])) continue;
                    $where = [];
                    $where[] = ['id', '=', $source['banquetPayment']['id']];
                    $model = \app\common\model\OrderBanquetPayment::where($where)->find();
                    if ($confirm->status == 0) {
                        echo "审核ID:".$confirm->id;
                        echo "\n";
                        echo "当前审核状态:" . $confirm->status;
                        echo "\n";
                        echo "婚宴付款审核状态" . $model->item_check_status;
                        echo "\n";
                    }

                } else if ($key == 'wedding') {
                    if (empty($source['wedding']['id'])) continue;
                    $where = [];
                    $where[] = ['id', '=', $source['wedding']['id']];
                    $model = \app\common\model\OrderWedding::where($where)->find();
                    if ($confirm->status == 0) {
                        echo "审核ID:".$confirm->id;
                        echo "\n";
                        echo "当前审核状态:" . $confirm->status;
                        echo "\n";
                        echo "婚庆审核状态" . $model->item_check_status;
                        echo "\n";
                    }
                } else if ($key == 'weddingIncome') {
                    if (empty($source['weddingIncome']['id'])) continue;
                    $where = [];
                    $where[] = ['id', '=', $source['weddingIncome']['id']];
                    $model = \app\common\model\OrderWeddingReceivables::where($where)->find();
                    if ($confirm->status == 0) {
                        echo "审核ID:".$confirm->id;
                        echo "\n";
                        echo "当前审核状态:" . $confirm->status;
                        echo "\n";
                        echo "婚庆收款审核状态" . $model->item_check_status;
                        echo "\n";
                    }

                } else if ($key == 'hotelItem') {
                    if (empty($source['hotelItem']['id'])) continue;
                    $where = [];
                    $where[] = ['id', '=', $source['hotelItem']['id']];
                    $model = \app\common\model\OrderHotelItem::where($where)->find();
                    if ($confirm->status == 0) {
                        echo "审核ID:".$confirm->id;
                        echo "\n";
                        echo "当前审核状态:" . $confirm->status;
                        echo "\n";
                        echo "酒店项目款审核状态" . $model->item_check_status;
                        echo "\n";
                    }

                } else if ($key == 'hotelProtocol') {
                    if (empty($source['hotelProtocol']['id'])) continue;
                    $where = [];
                    $where[] = ['id', '=', $source['hotelProtocol']['id']];
                    $model = \app\common\model\OrderHotelItem::where($where)->find();
                    if ($confirm->status == 0) {
                        echo "审核ID:".$confirm->id;
                        echo "\n";
                        echo "当前审核状态:" . $confirm->status;
                        echo "\n";
                        echo "酒店协议款审核状态" . $model->item_check_status;
                        echo "\n";
                    }

                } else {
                    echo $key;
                    echo "\n";
                }
            }

            echo "\n";
        }
    }
}