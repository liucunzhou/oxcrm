<?php
namespace app\index\controller\order;

use app\common\model\Brand;
use app\index\controller\Backend;

class Count extends Backend
{
    protected $UserModel = [];
    protected $OrderWeddingReceivables = [];
    protected $OrderBanquetReceivables = [];
    protected $OrderWeddingSuborder = [];
    protected $OrderBanquetSuborder = [];

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
    }

    public function index()
    {
        $param = $this->request->param();
        // $param['limit'] = isset($param['limit']) ? $param['limit'] : 1000;
        // $param['page'] = isset($param['page']) ? $param['page'] + 1 : 1;
        /**
        $config = [
            'page' => $param['page']
        ];
         * **/

        $map = [];
        // 搜索条件：company_id  newsTypesList  date_range
        if( !empty($param['company_id']) )
        {
            $map[] = ['company_id','=',$param['company_id']];
        }

        if( !empty($param['newsTypesList']) )
        {
            $map[] = ['news_type','=',$param['newsTypesList']];
        }

        $model = $this->model;
        if( !empty($param['date_range']) )
        {
            $arr = explode('~',$param['date_range']);
            $arr[0] = strtotime($arr[0]);
            $arr[1] = strtotime($arr[1]);
            $map[] = ['event_date','between',$arr];
        } else {
            $model = $this->model->whereTime('event_date', 'month');
        }

        $fields = "id,news_type,event_date,hotel_text,banquet_hall_name,bridegroom,bride,earnest_money,middle_money,tail_money,totals,salesman";
        // $list =  $this->model->where($map)->order('id desc')->field($fields)->paginate($param['limit'], false, $config);
        $list =  $model->where($map)->order('id desc')->field($fields)->select();

        $list = $list->toArray();
        foreach ($list as $k=>&$v){
            $v['event_date'] = $v['event_date'] != 0 ? substr($v['event_date'], 0, 10) : '-';
            $v['salesman'] = !empty($v['salesman']) ? $this->UserModel->getUser($v['salesman'])['realname'] : '-';
            $WeddingSuborder = $this->OrderWeddingSuborder->where('order_id',$k['id'])->column('wedding_total');
            $BanquetSuborder = $this->OrderBanquetSuborder->where('order_id',$k['id'])->column('banquet_totals');

            $v['totals_snum'] = $v['totals'] + $WeddingSuborder['0'] + $BanquetSuborder['0'];
            $v['tail_money'] = $v['totals_snum'] - $v['earnest_money'] - $v['middle_money'];

            $zdj = 0;
            $zzk = 0;
            $zwk = 0;
            $zex = 0;
            if($v['news_type'] == 1) {
                $res = $this->OrderWeddingReceivables
                    ->where('order_id', $v['id'])
                    ->field('wedding_income_type,wedding_income_item_price')
                    ->select();
                if (!empty($res)) {
                    foreach ( $res as $key=>&$vule ){
                        if ($vule['wedding_income_type'] == 1) {
                            $zdj += $vule['wedding_income_item_price'];
                        }

                        if ($vule['wedding_income_type'] == 2) {
                            $zzk += $vule['wedding_income_item_price'];
                        }

                        if ($vule['wedding_income_type'] == 3) {
                            $zwk += $res['wedding_income_item_price'];
                        }

                        if ($vule['wedding_income_type'] == 4) {
                            $zex += $res['wedding_income_item_price'];
                        }
                    }
                }
            } else {
                $res = $this->OrderBanquetReceivables
                    ->where('order_id', $v['id'])
                    ->field('banquet_income_type,banquet_income_item_price')
                    ->select();
                if( !empty($res) ){
                    foreach ( $res as $key=>&$vule ){
                        if ($vule['banquet_income_type'] == 1) {
                            $zdj += $vule['banquet_income_item_price'];
                        }

                        if ($vule['banquet_income_type'] == 2) {
                            $zzk += $vule['banquet_income_item_price'];
                        }

                        if ($vule['banquet_income_type'] == 3) {
                            $zwk += $res['banquet_income_item_price'];
                        }

                        if ($vule['banquet_income_type'] == 4) {
                            $zex += $res['banquet_income_item_price'];
                        }
                    }
                }
            }
            $v['ysdj'] = $zdj - $v['earnest_money'];
            $v['yszk'] = $zzk - $v['middle_money'];
            $v['yswk'] = $zwk + $zex - $v['tail_money'];
        }
        unset($list['news_type']);
        $sums = [
           '' => [
               'id'                   =>  'all',
               'event_date'          =>  '总计',
               'hotel_text'          =>  '',
               'banquet_hall_name'  =>  '',
               'bridegroom'          =>  '',
               'bride'               =>  '',
               'earnest_money'  => array_sum(array_column($list,'earnest_money')),
               'middle_money'   => array_sum(array_column($list,'middle_money')),
               'tail_money'     => array_sum(array_column($list,'tail_money')),
               'totals'          => array_sum(array_column($list,'totals')),
               'salesman'            =>  '-',
               'totals_snum'    => array_sum(array_column($list,'totals_snum')),
               'ysdj'            => array_sum(array_column($list,'ysdj')),
               'yszk'            => array_sum(array_column($list,'yszk')),
               'yswk'            => array_sum(array_column($list,'yswk'))
            ]
        ];
        $list = $list + $sums;
        $config = config();
        $this->assign('firmList',$this->FirmList);
        $this->assign('newsTypesList',$config['crm']['news_type_list']);
        $this->assign('list',$list);
        return $this->fetch('order/count/index');
    }
}