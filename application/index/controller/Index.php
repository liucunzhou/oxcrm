<?php
namespace app\index\controller;

use app\index\model\Auth;
use app\index\model\Module;
use app\index\model\User;
use app\index\model\UserAuth;
use think\facade\Session;

class Index extends Base
{
    public function index()
    {
        // 获取系统所有目录
        $modules = Module::getList();
        $nodes = \app\index\model\Auth::getMenuList();
        $user = session("user");
        if($user['nickname'] != 'admin') {
            $userAuth = UserAuth::getUserAuthSet($user['id']);
            $userAuthSet = explode(',', $userAuth['auth_set']);
        }

        $menus = [];
        foreach ($nodes as $key=>$node) {
            if($user['nickname'] == 'admin') {
                $k = $modules[$node['parent_id']];
                $menus[$k]['icon'] = 'icon-cogs';
                $menus[$k]['items'][] = [
                    'text' => $node['title'],
                    'url' => $node['route']
                ];
            } else if(in_array($node['id'], $userAuthSet)) {
                $k = $modules[$node['parent_id']];
                $menus[$k]['icon'] = 'icon-cogs';
                $menus[$k]['items'][] = [
                    'text' => $node['title'],
                    'url' => $node['route']
                ];
            }
        }
        $this->assign('menus', $menus);

        return $this->fetch();
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

}
