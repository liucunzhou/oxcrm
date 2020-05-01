<?php
namespace app\index\controller\order;

use app\index\controller\Backend;

class Count extends Backend
{
    protected $UserModel = [];
    protected $OrderWeddingReceivables = [];
    protected $OrderBanquetReceivables = [];
    protected $OrderWeddingSuborder = [];
    protected $OrderBanquetSuborder = [];

    protected $firmList = [
        [
            'company_id' => '25',
            'title' => '誉思'
            ],
        [
            'company_id' => '26',
            'title' => '红丝'
        ],
        [
            'company_id' => '24',
            'title' => '曼格纳'
        ]
    ];


    protected function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\Order();
        $this->UserModel = new \app\common\model\User();
        $this->OrderWeddingReceivables = new \app\common\model\OrderWeddingReceivables();
        $this->OrderBanquetReceivables = new \app\common\model\OrderBanquetReceivables();
        $this->OrderWeddingSuborder = new \app\common\model\OrderWeddingSuborder();
        $this->OrderBanquetSuborder = new \app\common\model\OrderBanquetSuborder();
    }

    public function index()
    {
        $param = $this->request->param();
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 10;
        $param['page'] = isset($param['page']) ? $param['page'] + 1 : 1;
        $config = [
            'page' => $param['page']
        ];

        $map = [];
        // 搜索条件：company_id  newsTypesList  date_range
        // $map[] = ['event_date','between',[$param['start'],$param['end']]];

        $fields = "id,news_type,event_date,hotel_text,banquet_hall_name,bridegroom,bride,earnest_money,middle_money,tail_money,totals,salesman";
        $list =  $this->model->where($map)->order('id desc')->field($fields)->paginate($param['limit'], false, $config);
        $list = $list->getCollection()->toArray();
        foreach ($list as $k=>&$v){
            $v['event_date'] = $v['event_date'] != 0 ? substr($v['event_date'], 0, 10) : '-';
            $v['salesman'] = !empty($v['salesman']) ? $this->UserModel->getUser($v['salesman'])['realname'] : '-';

            $WeddingSuborder = $this->OrderWeddingSuborder->where('order_id',$k['id'])->column('wedding_total');
            $BanquetSuborder = $this->OrderBanquetSuborder->where('order_id',$k['id'])->column('banquet_totals');

            $v['totals_snum'] = $v['totals'] + $WeddingSuborder['0'] + $BanquetSuborder['0'];

            if($v['news_type'] == 1) {
                $res = $this->OrderWeddingReceivables
                    ->where('order_id', $k['id'])
                    ->field('wedding_income_type,wedding_income_item_price')
                    ->select();

                if (!isset($res)) {
                    if ($res['wedding_income_type'] == 1) {
                        $v['ysdj'] = $res['wedding_income_item_price'];
                    }

                    if ($res['wedding_income_type'] == 2) {
                        $v['yszk'] = $res['wedding_income_item_price'];
                    }

                    if ($res['wedding_income_type'] == 3) {
                        $v['yswk'] = $res['wedding_income_item_price'];
                    }
                } else {
                    $v['ysdj'] = '0';
                    $v['yszk'] = '0';
                    $v['yswk'] = '0';
                }
            } else {
                $res = $this->OrderBanquetReceivables
                    ->where('order_id', $k['id'])
                    ->field('banquet_income_type,banquet_income_item_price')
                    ->select();

                if (!isset($res)) {
                    if ($res['banquet_income_type'] == 1) {
                        $v['ysdj'] = $res['banquet_income_item_price'];
                    }

                    if ($res['banquet_income_type'] == 2) {
                        $v['yszk'] = $res['banquet_income_item_price'];
                    }

                    if ($res['banquet_income_type'] == 3) {
                        $v['yswk'] = $res['banquet_income_item_price'];
                    }
                } else {
                    $v['ysdj'] = '0';
                    $v['yszk'] = '0';
                    $v['yswk'] = '0';
                }
            }
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
        $this->assign('firmList',$this->firmList);
        $this->assign('newsTypesList',$config['crm']['news_type_list']);
        $this->assign('list',$list);
        return $this->fetch('order/count/index');
    }
}