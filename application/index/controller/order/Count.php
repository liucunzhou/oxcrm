<?php
namespace app\index\controller\order;

use app\index\controller\Backend;

class Count extends Backend
{
    protected $OrderWeddingReceivables = [];
    protected $OrderBanquetReceivables = [];

    protected function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\Order();
        $this->OrderWeddingReceivables = new \app\common\model\OrderWeddingReceivables();
        $this->OrderBanquetReceivables = new \app\common\model\OrderBanquetReceivables();
    }

    public function index()
    {
        $param = $this->request->param();
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 100;
        $param['page'] = isset($param['page']) ? $param['page'] + 1 : 1;
        $config = [
            'page' => $param['page']
        ];

        $map = [];
        // $map[] = ['event_date','between',[$param['start'],$param['end']]];

        $fields = "id,news_type,event_date,hotel_text,banquet_hall_name,bridegroom,bride,earnest_money,middle_money,tail_money,totals,salesman";
        $list =  $this->model->where($map)->order('id desc')->field($fields)->paginate($param['limit'], false, $config);
        foreach ($list as $k=>$v){
            if($v['news_type'] == 1) {
                $res = $this->OrderWeddingReceivables
                    ->where('order_id', $k['id'])
                    ->field('wedding_income_type,wedding_income_item_price')
                    ->select();

                if (!isset($res)) {
                    if ($res['wedding_income_type'] == 1) {
                        $list[$k]['ysdj'] = $res['wedding_income_item_price'];
                    }

                    if ($res['wedding_income_type'] == 2) {
                        $list[$k]['yszk'] = $res['wedding_income_item_price'];
                    }

                    if ($res['wedding_income_type'] == 3) {
                        $list[$k]['yswk'] = $res['wedding_income_item_price'];
                    }
                } else {
                    $list[$k]['ysdj'] = '';
                    $list[$k]['yszk'] = '';
                    $list[$k]['yswk'] = '';
                }
            } else {
                $res = $this->OrderBanquetReceivables
                    ->where('order_id', $k['id'])
                    ->field('banquet_income_type,banquet_income_item_price')
                    ->select();

                if (!isset($res)) {
                    if ($res['banquet_income_type'] == 1) {
                        $list[$k]['ysdj'] = $res['banquet_income_item_price'];
                    }

                    if ($res['banquet_income_type'] == 2) {
                        $list[$k]['yszk'] = $res['banquet_income_item_price'];
                    }

                    if ($res['banquet_income_type'] == 3) {
                        $list[$k]['yswk'] = $res['banquet_income_item_price'];
                    }
                } else {
                    $list[$k]['ysdj'] = '';
                    $list[$k]['yszk'] = '';
                    $list[$k]['yswk'] = '';
                }
            }
        }
        $sum = 0;

        /**
        foreach($list as $item=>$items){
            $sums = [
               'sums' => [
                   'id' =>  '总计',
                   'event_date' =>  '',
                   'hotel_text' =>  '',
                   'banquet_hall_name' =>  '',
                   'bridegroom' =>  '',
                   'bride' =>  '',
                   'earnest_money_sum' => $sum += (int) $item['earnest_money'],
                   'middle_money_sum' => $sum += (int) $item['middle_money'],
                   'tail_money_sum' => $sum += (int) $item['tail_money'],
                   'contract_totals_sum' => $sum += (int) $item['contract_totals'],
                   'ysdj_sum' => $sum += (int) $item['ysdj'],
                   'yszk_sum' => $sum += (int) $item['yszk'],
                   'yswk_sum' => $sum += (int) $item['yswk']
                ]
            ];
        }
        $list = $list + $sums;
         * **/
        $this->assign('list',$list);
        return $this->fetch('order/count/index');
    }
}