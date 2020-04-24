<?php
namespace app\h5\controller\dictionary;


use app\h5\controller\Base;
use think\response\Json;

class Region extends Base
{

    public $model = '';

    protected function initialize()
    {
        $this->model = new \app\common\model\Region();
    }


    public function getRegion()
     {
         $cityList = [
             '802' => '上海市',
             '1965' => '广州市',
             '934' => '杭州市',
             '861' => '苏州市'
         ];

         $data = [];
         foreach ($cityList as $key=>$value) {
             $fields = 'id,pid,shortname as title';
             $row = \app\common\model\Region::getAreaList($key,$fields);
             $row = array_values($row);

             $data[] = [
                 'id'       => $key,
                 'title'    => $value,
                 'items'    => $row
             ];
         }
         $result = [
             'code' => '200',
             'msg'  =>  '获取城市数据',
             'data' =>  [
                 'region' =>    $data
             ]
         ];
         return json($result);
     }
}