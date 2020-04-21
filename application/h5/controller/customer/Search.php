<?php
namespace app\h5\controller\customer;

use app\common\model\Intention;
use app\common\model\User;
use app\h5\controller\Base;

class Search extends Base
{

    public function items()
    {

        ###  跟进状态
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

        ###  获取方式
        $alls = ['all'=>'全部方式'];
        $allocateTypeList = $this->allocateTypes;
        $allocateTypeList = $alls + $allocateTypeList;

        ###  人员列表
        $userList = [];
        if ($this->role['auth_type'] != 0) {
            // 管理者
            $fields = 'id,realname as title';
            $users = ['all'=>'全部'];
            $userList = User::getUsersInfoByDepartmentId($this->user['department_id'], false, $fields);
            $userList = $users + $userList;
        }

        ### range
        $rangeList = [
            [
                'id'    => 'all',
                'title' => '全部时间'
            ],
            [
                'id'    => 'd',
                'title' => '今天'
            ],
            [
                'id'    => 'w',
                'title' => '本周'
            ],
            [
                'id'    => 'm',
                'title' => '本月'
            ],
            [
                'id'    => 'start_date',
                'title' => '开始时间'
            ],
            [
                'id'    => 'end_date',
                'title' => '结束时间'
            ]
        ];

        $data = [
            'active_status'     => [
                'text'  => '跟进状态',
                'items' => $statusList
            ],
            'allocate_type' => [
                'text'  => '获取方式',
                'items' => $allocateTypeList
            ],
            'user_id'   => [
                'text'  => '员工列表',
                'items' => $userList
            ],
            'range'     => [
                'text'  => '时间区间',
                'items' => $rangeList
            ]
        ];

        $result = [
            'code'  => '200',
            'data'  =>  $data

        ];

        return json($result);
    }

}