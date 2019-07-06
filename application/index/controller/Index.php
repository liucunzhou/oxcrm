<?php
namespace app\index\controller;

use app\index\model\UserAuth;
use think\facade\Session;

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

            '系统工具' => [
                'icon'  => 'icon-lock',
                'items' => [
                    [
                        'text'  => '生成模型',
                        'url'   => 'System/createModel'
                    ],
                    [
                        'text'  => '生成模块',
                        'url'   => 'System/createController'
                    ],
                    [
                        'text'  => '生成列表',
                        'url'   => 'System/createView'
                    ],
                    [
                        'text'  => '生成表单',
                        'url'   => 'System/createForm'
                    ],

                ]
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
                    ]
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
                    ]
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
                        'url' => 'Customer/apply'
                    ]
                ],
            ],

            '订单管理' => [
                'icon' => 'icon-laptop',
                'items' => [
                    [
                        'text' => '一站式',
                        'url' => 'Order/index',
                    ],
                    [
                        'text' => '婚庆订单',
                        'url' => 'Order/wedding',
                    ],
                    [
                        'text' => '婚宴订单',
                        'url' => 'Order/banquet',
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
                        'text' => '每小时数据',
                        'url' => 'Count/hour',
                    ],
                    [
                        'text' => '数据对比',
                        'url' => 'Count/compare',
                    ],
                ],
            ]
        ];
        $user = Session::get("user");
        $auth = UserAuth::getUserLogicAuth($user['id']);
        if(empty($auth['role_ids']) && $user['nickname'] != 'admin') return $this->fetch();


        $roles = explode(',', $auth['role_ids']);
        ### 根据角色自动判断条件
        if (in_array(3,$roles)) {
            ## 客服主管
            unset($menus['系统管理']);
            unset($menus['权限管理']);
        } else if(in_array(1, $roles)) {
            ## 客服
            unset($menus['系统管理']);
            unset($menus['权限管理']);
            unset($menus['客资管理']);
            unset($menus['组织架构']['items'][0]);
            unset($menus['组织架构']['items'][1]);
            unset($menus['数据统计']);
        } else if(in_array(5, $roles)) {
            ## 门店店长
            // $map[] = ['store_id', ''];
            unset($menus['系统管理']);
            unset($menus['权限管理']);
            unset($menus['客资管理']);
            unset($menus['组织架构']['items'][0]);
            unset($menus['组织架构']['items'][1]);
        } else if(in_array(4, $roles)) {
            unset($menus['系统管理']);
            unset($menus['权限管理']);
            unset($menus['客资管理']);
            unset($menus['组织架构']['items'][0]);
            unset($menus['组织架构']['items'][1]);
            unset($menus['数据统计']);
        } else if($user['nickname'] != 'admin') {
            $menus = [];
        } else {
            // unset($menus['跟进管理']);
        }
        $this->assign('menus', $menus);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

}
