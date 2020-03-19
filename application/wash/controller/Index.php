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
                        'url'   => '/customer.customer/index'
                    ],
                    [
                        'text'  => '今日跟进',
                        'url'   => '/customer.customer/today'
                    ],
                    [
                        'text'  => '客资公海',
                        'url'   => '/customer.customer/sea',
                    ],
                ]
            ],
        ];
        $this->assign('menus', $menus);

        return $this->fetch();
    }
}
