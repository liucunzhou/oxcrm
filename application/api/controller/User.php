<?php
/**
 * Created by PhpStorm.
 * User: xiaozhu
 * Date: 2019/7/22
 * Time: 10:41
 */

namespace app\api\controller;

use think\Request;

class User extends Base
{
    public function repassword()
    {
        $id = $this->user['id'];
        $user = \app\common\model\User::get($id);

        if ($user['password'] != md5($this->params['password'])) {
            return xjson([
                'code' => '500',
                'msg' => '原密码不正确'
            ]);
        }

        $newpassword = md5($this->params['newpassword']);
        $result = $user->save(['password' => $newpassword]);

        if ($result) {
            return xjson([
                'code' => '200',
                'msg' => '密码修改成功'
            ]);
        } else {
            return xjson([
                'code' => '501',
                'msg' => '密码修改失败'
            ]);
        }
    }

    public function editUser()
    {
        $id = $this->user['id'];
        $user = \app\common\model\User::get($id);
        $result = $user->save(['realname' => $this->params['realname']]);
        if ($result) {
            return xjson([
                'code' => '200',
                'msg' => '用户昵称修改成功'
            ]);
        } else {
            return xjson([
                'code' => '501',
                'msg' => '用户昵称修改失败'
            ]);
        }
    }

    public function bindUUid()
    {
        $id = $this->user['id'];
        $user = \app\common\model\User::get($id);
        $result = $user->save(['uuid' => $this->params['uuid']]);
        if ($result) {
            return xjson([
                'code' => '200',
                'msg' => '同步UUID成功'
            ]);
        } else {
            return xjson([
                'code' => '501',
                'msg' => '同步UUID成功'
            ]);
        }
    }
}