<?php

namespace app\common\command;

use app\common\model\Hotel;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\MemberRelation;
use app\common\model\Mobile;
use app\common\model\MobileRelation;
use app\common\model\OrderBanquet;
use app\common\model\OrderWedding;
use app\common\model\Store;
use app\common\model\User;
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
        $action = '';
        if ($input->hasOption("action")) {
            $action = $input->getOption("action");
        } else {
            $output->writeln("请输入要执行的操作");
            return false;
        }


        switch ($action) {
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
        }
    }

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
                // $data['salesman'] = $user->id;
                // $result = $row->save($data);
                // echo $row->getLastSql();
                // echo "\n";
                $result = 1;
                if (!$result) {
                    echo $row->sale . "订单匹配失败\n";
                }
            } else {
                file_put_contents('./member.txt', $row->bride_mobile . ":::" . $row->bridegroom_mobile . ":::".$row->salesman."\n", FILE_APPEND);
                echo $row->sale . "未匹配\n";
            }
        }
    }

    public function initMemberByRelation()
    {
        $file = file_get_contents("./member.txt");
        $rows = explode("\n", $file);

        foreach ($rows as $row) {
            $arr = explode(":::", $row);
            if(count($arr) < 3) continue;
            if (!empty($arr[0]) && empty($arr[1])) {
                $where = [];
                $where[] = ['mobiles', 'like', '%{$arr[0]}%'];
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
}
