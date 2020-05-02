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
        // $where[] = ['type', '<>', 'wash'];
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
        $statusLists = array_merge($all, $statusList);

        ###  获取方式
        $alls = [
            0   => [
                'id'    => 'all',
                'title' => '全部方式'
            ]
        ];
        $allocateTypeList = [];
        foreach ($this->allocateTypes as $key=>$value) {
            $allocateTypeList[] = [
                'id'    => $key,
                'title' => $value
            ];
        }
        $allocateTypeList = array_merge($alls, $allocateTypeList);

        ###  人员列表
        $userList = [];
        if ($this->role['auth_type'] > 0) {
            // 管理者
            $users = [
                0 => [
                    'id'    => 'all',
                    'title' => '全部'
                ]
            ];
            $fields = 'id,realname as title,nickname';
            $userList = User::getUsersInfoByDepartmentId($this->user['department_id'], false, $fields);
            $userList = array_values($userList);
            // print_r($userList);
            $userList = array_merge($users, $userList);
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
                'items' => $statusLists
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
        // if(empty($userList)) unset($data['user_id']);

        $result = [
            'code'  => '200',
            'msg'   => '获取数据成功',
            'data'  => $data
        ];

        return json($result);
    }

    public function newsType()
    {
        $arr = [];
        $newsTypeList = $this->config['news_type_list'];
        foreach ($newsTypeList as $key=>$value) {
            $arr[] = [
                'id'    => $key,
                'title' => $value
            ];
        }

        $all = [
           0 => [
               'id'     => 'all',
               'title'  => '请选择'
           ]
        ];
        $arr = array_merge($all, $arr);


        $result = [
            'code'  => '200',
            'msg'   => '获取数据成功',
            'data'  => [
                'newsTypeList'  => $arr
            ]
        ];
        return json($result);
    }
}