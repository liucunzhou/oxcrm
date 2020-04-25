<?php
namespace app\h5\controller\order;

use app\common\model\Brand;
use app\common\model\User;
use app\h5\controller\Base;

class Search extends Base
{

    protected function initialize()
    {
        return parent::initialize();
    }

    public function items()
    {

        ##  审核状态
        $arr = $this->config['check_status'];
        $checkStautsList = [];
        foreach ($arr as $key=>$value) {
            $checkStautsList[] = [
                'id'    => $key,
                'title' => $value
            ];
        }
        $all = [
            0 => [
                'id'    => 'all',
                'title' => '所有状态'
            ]
        ];
        $checkStautsList = $all + $checkStautsList;

        ###  签约公司
        $field = "id,title";
        $list = Brand::field($field)->order('is_valid desc,sort desc,id asc')->select();
        $brandList = $list->toArray();
        $all = [
            0 => [
                'id'    => 'all',
                'title' => '所有公司'
            ]
        ];
        $brandList = $all + $brandList;

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
            $userList = $users + $userList;
        }

        ##  时间类型
        $rangeTypeList = [
            [
                'id'    =>  'create_time',
                'title' =>  '录入时间'
            ],
            [
                'id'    =>  'event_time',
                'title' =>  '举办时间'
            ],
            [
                'id'    =>  'sign_time',
                'title' =>  '签单时间'
            ],
        ];

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
            'check_status' => [
                'text'  => '审核状态',
                'items' => $checkStautsList
            ],
            'company_id'     => [
                'text'  => '签约公司',
                'items' => $brandList
            ],
            'user_id'   => [
                'text'  => '人员列表',
                'items' => $userList
            ],
            'range_type'     => [
                'text'  => '时间类型',
                'items' => $rangeTypeList
            ],
            'range'     => [
                'text'  => '时间区间',
                'items' => $rangeList
            ]
        ];
        // if(empty($userList)) unset($data['user_id']);

        $result = [
            'code'  => '200',
            'data'  =>  $data
        ];

        return json($result);
    }

}