<?php

namespace app\index\controller\order;

use app\common\model\Brand;
use app\common\model\Store;
use app\index\controller\Backend;

class Count extends Backend
{
    protected $UserModel = [];
    protected $OrderWeddingReceivables = [];
    protected $OrderBanquetReceivables = [];
    protected $OrderWeddingSuborder = [];
    protected $OrderBanquetSuborder = [];
    protected $hotelList = [];

    protected function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\Order();
        $this->UserModel = new \app\common\model\User();
        $this->FirmList = Brand::getBrands();
        $this->OrderWeddingReceivables = new \app\common\model\OrderWeddingReceivables();
        $this->OrderBanquetReceivables = new \app\common\model\OrderBanquetReceivables();
        $this->OrderWeddingSuborder = new \app\common\model\OrderWeddingSuborder();
        $this->OrderBanquetSuborder = new \app\common\model\OrderBanquetSuborder();

        $this->hotelList = Store::getStoreList();
        $this->assign('hotelList', $this->hotelList);
    }

    public function index()
    {
        $param = $this->request->param();
        $map = [];
        $map[] = ['item_check_status', '=', '2'];
        $map[] = ['complete', '<>', '101'];
        // 搜索条件：company_id  newsTypesList  date_range
        if (!empty($param['company_id'])) {
            if (count($param['company_id']) > 1) {
                $map[] = ['company_id', 'in', $param['company_id']];
            } else {
                $map[] = ['company_id', '=', $param['company_id'][0]];
            }
        }

        if (!empty($param['news_type'])) {
            if (count($param['news_type']) > 1) {
                $map[] = ['news_type', 'in', $param['news_type']];
            } else {
                $map[] = ['news_type', '=', $param['news_type'][0]];
            }
        }

        if (!empty($param['hotel_id'])) {
            if (count($param['hotel_id']) > 1) {
                $map[] = ['hotel_id', 'in', $param['hotel_id']];
            } else {
                $map[] = ['hotel_id', '=', $param['hotel_id'][0]];
            }
        }

        $model = new \app\common\model\Order();
        if (!empty($param['date_range'])) {
            $arr = explode('~', $param['date_range']);
            $arr[0] = strtotime($arr[0]);
            $arr[1] = strtotime($arr[1]);
            $map[] = ['event_date', 'between', $arr];
        } else {
            $model = $this->model->whereTime('event_date', 'month');
        }

        $fields = "id,news_type,company_id,event_date,hotel_id,hotel_text,cooperation_mode,banquet_hall_name,bridegroom,bride,earnest_money,middle_money,tail_money,totals,salesman";
        $list = $model->where($map)->order('event_date asc,id desc')->field($fields)->select();

        $list = $list->toArray();
        foreach ($list as $k => &$v) {
            $v['company_id'] = !empty($v['company_id']) ? $this->FirmList[$v['company_id']]['title'] : '-';
            $v['event_date'] = $v['event_date'] != 0 ? substr($v['event_date'], 0, 10) : '-';
            $v['salesman'] = !empty($v['salesman']) ? $this->UserModel->getUser($v['salesman'])['realname'] : '-';
            $where = [];
            $where[] = ['item_check_status', '=', '2'];
            $where[] = ['order_id', '=', $v['id']];
            $WeddingSuborder = $this->OrderWeddingSuborder->where($where)->sum('wedding_totals');
            $BanquetSuborder = $this->OrderBanquetSuborder->where($where)->sum('banquet_totals');

            $v['totals_snum'] = $v['totals'] + $WeddingSuborder + $BanquetSuborder;
            $v['tail_money'] = $v['totals_snum'] - $v['earnest_money'] - $v['middle_money'];
            if (empty($v['hotel_text'])) {
                $v['hotel_text'] = !empty($v['hotel_id']) ? $this->hotelList[$v['hotel_id']]['title'] : '-';
            }

            // 总计定金
            $zdj = 0;
            // 总计中款
            $zzk = 0;
            // 总计尾款
            $zwk = 0;
            // 总计二销
            $zex = 0;

            // 1定金、2中款、3尾款、4意向金、5 二销

            $where = [];
            $where[] = ['item_check_status', '=', '2'];
            $where[] = ['order_id', '=', $v['id']];

            $res = $this->OrderWeddingReceivables
                ->where($where)
                ->field('wedding_income_type,wedding_income_item_price')
                ->select();

            if (!empty($res)) {
                foreach ($res as $key => &$value) {
                    if ($value['wedding_income_type'] == '1') {
                        $zdj += $value['wedding_income_item_price'];
                    }

                    if ($value['wedding_income_type'] == '2') {
                        $zzk += $value['wedding_income_item_price'];
                    }

                    if ($value['wedding_income_type'] == '3') {
                        $zwk += $value['wedding_income_item_price'];
                    }

                    if ($value['wedding_income_type'] == '5') {
                        $zex += $value['wedding_income_item_price'];
                    }
                }
            }

            $where = [];
            $where[] = ['item_check_status', '=', '2'];
            $where[] = ['order_id', '=', $v['id']];
            $res = $this->OrderBanquetReceivables
                ->where($where)
                ->field('banquet_income_type,banquet_income_item_price')
                ->select();

            if (!empty($res)) {
                foreach ($res as $key => &$value) {

                    if ($value['banquet_income_type'] == '1') {
                        $zdj += $value['banquet_income_item_price'];
                    }

                    if ($value['banquet_income_type'] == '2') {
                        $zzk += $value['banquet_income_item_price'];
                    }

                    if ($value['banquet_income_type'] == '3') {
                        $zwk += $value['banquet_income_item_price'];
                    }

                    if ($value['banquet_income_type'] == '5') {
                        $zex += $value['banquet_income_item_price'];
                    }
                }
            }

            // 应收定金
            $v['ysdj'] = $zdj - $v['earnest_money'];
            // 应收中款
            $v['yszk'] = $zzk - $v['middle_money'];
            // 应收尾款, zwk 已收尾款, zex 已收二销
            $v['yswk'] = $zdj + $zzk + $zwk + $zex - $v['totals_snum'];

        }

        unset($list['news_type']);
        $sums = [
            '' => [
                'id' => 'all',
                'event_date' => '总计',
                'hotel_text' => '',
                'banquet_hall_name' => '',
                'bridegroom' => '',
                'bride' => '',
                'earnest_money' => array_sum(array_column($list, 'earnest_money')),
                'middle_money' => array_sum(array_column($list, 'middle_money')),
                'tail_money' => array_sum(array_column($list, 'tail_money')),
                'totals' => array_sum(array_column($list, 'totals')),
                'salesman' => '-',
                'totals_snum' => array_sum(array_column($list, 'totals_snum')),
                'ysdj' => array_sum(array_column($list, 'ysdj')),
                'yszk' => array_sum(array_column($list, 'yszk')),
                'yswk' => array_sum(array_column($list, 'yswk'))
            ]
        ];
        $config = config();
        $list = $list + $sums;
        foreach ($list as $k => &$v) {
            $v['cooperation_mode'] = !empty($v['cooperation_mode']) ? $config['crm']['cooperation_mode'][$v['cooperation_mode']] : '-';
            // 应收中款
            $v['ysdj'] = $this->plus_minus_conversion($v['ysdj']);
            // 应收中款
            $v['yszk'] = $this->plus_minus_conversion($v['yszk']);
            // 应收尾款
            $v['yswk'] = $this->plus_minus_conversion($v['yswk']);
        }
        $this->assign('firmList', $this->FirmList);
        $this->assign('newsTypesList', $config['crm']['news_type_list']);
        $this->assign('list', $list);
        return $this->fetch('order/count/index');
    }

    function plus_minus_conversion($number = 0)
    {
        return $number > 0 ? -1 * $number : abs($number);
    }
}