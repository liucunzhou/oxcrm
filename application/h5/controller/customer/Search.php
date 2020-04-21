<?php
namespace app\h5\controller\customer;

use app\common\model\Intention;
use app\h5\controller\Base;

class Search extends Base
{

    public function items(){

        $where = [];
        $where[] = ['type', '<>', 'wash'];
        $field = "id,title,color";
        $list = Intention::where($where)->field($field)->order('is_valid desc,sort desc,id asc')->select();
        $statusList = $list->toArray();
        $all = [
            0 => [
                'id'    => 'all',
                'title' => '所有状态',
                'color' => '#ffffff'
            ]
        ];

        $statusList = $all + $statusList;
        $data = [
            'statusList'        => $statusList,
            'allocateTypeList'  => $this->allocateTypes
        ];

        $result = [
            'code'  => '200',
            'data'  =>  $data
        ];
        return json($result);
    }

}