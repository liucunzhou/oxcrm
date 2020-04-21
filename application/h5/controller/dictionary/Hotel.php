<?php
namespace app\h5\controller\dictionary;

use app\common\model\Store;
use app\h5\controller\Base;

class Hotel extends Base
{
    public $model = '';

    protected function initialize()
    {
        $this->model = new Store();
    }

    public function index()
    {
        $field = 'id,title';
        $hotelList = $this->model->field($field)->order('sort desc')->select();

        $result = [
            'code'  => '200',
            'msg'   => '获取酒店列表成功',
            'data'  => [
                'hotelList' => $hotelList
            ]
        ];
        return $result;
    }
}