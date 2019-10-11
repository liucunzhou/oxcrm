<?php
namespace app\index\controller;

use app\common\model\Auth;
use app\common\model\Module;
use app\common\model\User;
use app\common\model\UserAuth;
use think\facade\Request;
use think\facade\Session;

class Index extends Base
{
    public function index()
    {
        if (Request::isMobile()) {
            $this->redirect(url('user/info'));
            return false;
        }

        // 获取系统所有目录
        $modules = Module::getList();
        $nodes = \app\common\model\Auth::getMenuList();
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

    public function requestReply()
    {
        return $this->fetch();
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

}
