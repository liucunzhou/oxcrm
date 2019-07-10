<?php
namespace app\index\controller;

use app\index\model\Module;
use app\index\model\UserAuth;
use think\facade\Session;

class Index extends Base
{
    public function index()
    {
        $modules = Module::getList();
        $nodes = \app\index\model\Auth::getMenuList();
        $menus = [];
        foreach ($nodes as $key=>$node) {
            $k = $modules[$node['parent_id']];
            $menus[$k]['icon']  = 'icon-cogs';
            $menus[$k]['items'][] = [
                'text'  => $node['title'],
                'url'   => $node['route']
            ];
        }
        $this->assign('menus', $menus);
        return $this->fetch();
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

}
