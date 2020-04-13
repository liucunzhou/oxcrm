<?php

namespace app\wash\controller\ucenter;

use think\Controller;
use think\facade\Request;
use think\facade\Session;

class Passport extends Controller
{
    public function login()
    {
        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doLogin()
    {
        $post = Request::param();
        $Validate = new \app\index\validate\User();
        $valid = $Validate->check($post);
        if(!$valid) {
            $this->result([], 500, $Validate->getError());
        }

        $userModel = new \app\common\model\User();
        $where = [];
        $where['nickname'] = $post['nickname'];
        $where['password'] = md5($post['password']);
        $user = $userModel->where($where)->find();
        if(!$user) {
            $this->result([], 501, '密码不正确');
        }

        $path = url('wash/index/index');
        if($user['is_valid'] == 0){
            $this->result([], 502, '账号已经下线');
        }

        $washGroupIds = [2, 7, 15];
        if(!in_array($user['role_id'], $washGroupIds)) {
            $this->result([], 503, '非清洗组用户');
        }

        Session::set('user', $user);
        $data = [
            'redirect' => $path
        ];

        $this->result($data, 200, '登录成功');
    }

    public function logout()
    {
        Session::clear();
        $this->redirect('ucenter.passport/login');
    }
}
