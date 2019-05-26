<?php
namespace app\index\controller;

class Index extends Base
{
    public function index()
    {
        $menus = [
            '系统管理' => [
                'icon' => 'icon-cogs',
                'items' => [
                    [
                        'text' => '酒店管理',
                        'url' => 'Hotel/index'
                    ],
                    [
                        'text' => '品牌管理',
                        'url' => 'Brand/index'
                    ],
                    [
                        'text' => '门店管理',
                        'url' => 'Store/index'
                    ],
                    [
                        'text' => '来源管理',
                        'url' => 'Source/index'
                    ],
                    [
                        'text' => '意向管理',
                        'url' => 'intention/index'
                    ]
                ],
            ],

            '权限管理' => [
                'icon' => 'icon-lock',
                'items' => [
                    [
                        'text' => '模块管理',
                        'url' => 'Auth/index'
                    ],
                    [
                        'text' => '角色管理',
                        'url' => 'Auth/group'
                    ],
//                    [
//                        'text' => '操作日志',
//                        'url' => 'Operation/index'
//                    ]
                ],
            ],



            '组织架构'  => [
                'icon'  => 'icon-folder-open',
                'items' => [
                    [
                        'text'  => '部门管理',
                        'url'   => 'Department/index'
                    ],
                    [
                        'text' => '员工列表',
                        'url' => 'User/index'
                    ],
                    [
                        'text' => '个人中心',
                        'url' => 'User/info',
                    ],
                    [
                        'text' => '更新密码',
                        'url' => 'User/repassword',
                    ]
                ]
            ],

            '客资管理'  => [
                'icon' => 'icon-user',
                'items' => [
                    [
                        'text' => '客资列表',
                        'url' => 'Customer/index'
                    ],
                    [
                        'text' => '批量导入',
                        'url' => 'Customer/import'
                    ],
//                    [
//                        'text' => '推广咨费',
//                        'url' => 'Promotion/index',
//                    ],
                ],
            ],

            '跟进管理'  => [
                'icon' => 'icon-time',
                'items' => [
                    [
                        'text' => '客资公海',
                        'url' => 'Customer/index'
                    ],
                    [
                        'text' => '我的客资',
                        'url' => 'Customer/mine'
                    ],
                    [
                        'text' => '我的申请',
                        'url' => 'Customer/reply'
                    ],
                    [
                        'text' => '我的收藏',
                        'url' => 'Customer/favourite'
                    ]
                ],
            ],

            '订单管理' => [
                'icon' => 'icon-laptop',
                'items' => [
                    [
                        'text' => '成单申请',
                        'url' => 'Customer/orderApply',
                    ],
                    [
                        'text' => '订单查询',
                        'url' => 'Order/index',
                    ],
                    [
                        'text' => '定金催单',
                        'url' => 'Order/index',
                    ]
                ],
            ],

            '数据统计' => [
                'icon' => 'icon-bar-chart',
                'items' => [
                    [
                        'text' => '数据总览',
                        'url' => 'Count/index',
                    ],
                    [
                        'text' => '推广转化',
                        'url' => 'Count/promoter',
                    ],
                    [
                        'text' => '部门实收',
                        'url' => 'Count/sales',
                    ],
                    [
                        'text' => '每小时数据',
                        'url' => 'Count/hour',
                    ],
                    [
                        'text' => '数据对比',
                        'url' => 'Count/hour',
                    ],
                ],
            ]
        ];
        $this->assign('menus', $menus);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

}
