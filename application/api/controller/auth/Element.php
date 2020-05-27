<?php

namespace app\api\controller\auth;

use think\Controller;

class Element extends Controller
{
    public function menu()
    {
        $data = [
            [
                'icon'  => 'el-icon-lx-home',
                'index' => '/dashboard',
                'title' => '系统首页'
            ],
            [
                'icon' => 'el-icon-lx-calendar',
                'title' => '客资管理',
                'index' => '/customer',
                'subs' => [
                    /**
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/member/promoter',
                        'title' => '邀约客资',
                    ],
                    **/
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/customer/today',
                        'title' => '今日跟进',
                    ],
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/customer/mine',
                        'title' => '我的客资',
                    ],
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/customer/all',
                        'title' => '所有客资',
                    ],
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/customer/sea',
                        'title' => '客资公海',
                    ],
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/customer/recycle',
                        'title' => '回收站',
                    ]
                ]
            ],
            [
                'icon' => 'el-icon-lx-calendar',
                'title' => '数据统计',
                'index' => '/count',
                'subs' => [
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/count/wash/upload',
                        'title' => '上传统计',
                    ],
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/count/wash/call',
                        'title' => '通话统计',
                    ]
                ]
            ],
            /**
            [
                'icon' => 'el-icon-lx-calendar',
                'title' => '订单管理',
                'index' => '/order',
                'subs' => [
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/order/order/mine',
                        'title' => '我的订单',
                    ],
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/order/order/all',
                        'title' => '所有订单',
                    ],
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/order/order/recycle',
                        'title' => '回收站',
                    ]
                ]
            ],
            [
                'icon' => 'el-icon-lx-calendar',
                'title' => '订单审核',
                'index' => '/confirm',
                'subs' => [
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/confirm/confirm/mine',
                        'title' => '我的审核',
                    ],
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/confirm/confirm/all',
                        'title' => '所有审核',
                    ],
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/confirm/confirm/recycle',
                        'title' => '回收站',
                    ]
                ]
            ],
            [
                'icon' => 'el-icon-lx-calendar',
                'title' => '组织架构',
                'index' => '/company',
                'subs' => [
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/company/department/index',
                        'title' => '部门管理',
                    ],
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/company/user/index',
                        'title' => '用户管理',
                    ],
                    [
                        'icon' => 'el-icon-lx-calendar',
                        'index' => '/company/user/recycle',
                        'title' => '回收站',
                    ]
                ]
            ]
            ***/
        ];
        $result = [
            'code' => '200',
            'msg' => '获取数据成功',
            'data' => [
                'menus' => $data,
            ]
        ];
        return json($result);
    }
}