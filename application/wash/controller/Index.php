<?php
namespace app\wash\controller;

use think\Controller;
use think\Request;

class Index extends Controller
{
    public function index()
    {
        $menus = [
            '客资管理' => [
                'items' => [
                    [
                        'text'  => '我的客资',
                        'url'   => url('/wash/customer.customer/index')
                    ],
                    [
                        'text'  => '今日跟进',
                        'url'   => url('/wash/customer.customer/today')
                    ],
                    [
                        'text'  => '客资公海',
                        'url'   => url('/wash/customer.customer/sea'),
                    ],
                ]
            ],
        ];
        $this->assign('menus', $menus);

        return $this->fetch();
    }

    public function compare()
    {
        $citys = [
            '0'     => 0,
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

        $store = new \app\common\model\Store();
        $storeList = $store->select();

        $hotel = new \app\common\model\Hotel();
        $hotelList = $hotel->select();
        // print_r($hotelList);
        foreach($hotelList as $row) {
            $hotelName = trim($row->ho_name);
            $matched = false;
            foreach($storeList as $line) {
                $storeName = trim($line->title);
                if($hotelName == $storeName) {
                    $matched = true;
                    $data = [];
                    $data['wechat_id'] = $row->id;
                    // $data['sttore_no'] = '';
                    $data['brand_id'] =  0;
                    $data['admin_id'] = 1;
                    $data['is_entire'] = $row->ho_money_max;
                    $data['titlepic'] = $row->ho_coverurl;
                    $data['images'] = $row->ho_carouselurll.':::'.$row->ho_carouselurle.':::'.$row->ho_carouselurls.':::'.$row->ho_carouselurlsh;
                    $data['type'] = $row->ho_type;
                    $data['characteristics'] = $row->ho_characteristics;
                    $data['province_id'] = 0;
                    $data['city_id'] = $citys[$row->ho_city];
                    $data['area_id'] = $areas[$row->ho_area];
                    $data['min_price'] = $row->ho_money_min;
                    $data['star'] = $row->ho_drill;
                    $data['address'] = $row->ho_adddres;
                    $rs = $line->save($data);
                    echo "同步酒店成功---------------<br>";
                    echo $line->getLastSql()."<br>";
                    var_dump($rs);
                }
            }

            if(!$matched) {
                echo $hotelName.'<br>'; //."--------------------未匹配到该酒店<br>";
                $data = [];
                $data['wechat_id'] = $row->id;
                // $data['s'] = '';
                $data['brand_id'] =  0;
                $data['admin_id'] = 1;
                $data['is_entire'] = $row->ho_money_max;
                $data['title'] = $row->ho_name;
                $data['titlepic'] = $row->ho_coverurl;
                $data['images'] = $row->ho_carouselurll.':::'.$row->ho_carouselurle.':::'.$row->ho_carouselurls.':::'.$row->ho_carouselurlsh;
                $data['type'] = $row->ho_type;
                $data['characteristics'] = $row->ho_characteristics;
                $data['province_id'] = 0;
                $data['city_id'] = $citys[$row->ho_city];
                $data['area_id'] = $areas[$row->ho_area];
                $data['min_price'] = $row->ho_money_min;
                $data['star'] = $row->ho_drill;
                $data['address'] = $row->ho_adddres;
                $store = new \app\common\model\Store();
                $rs = $store->save($data);
                var_dump($rs);
            }
        }
    }
}
