<?php

namespace app\wash\controller\ucenter;

use app\common\model\AuthGroup;
use app\wash\controller\Backend;
use think\Request;

class User extends Backend
{
    protected function initialize()
    {
        parent::initialize();

        $this->model = new \app\common\model\User();
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    /**
     * 用户中心
     */
    public function info()
    {
        $user = \think\facade\Session::get("user");

        $roles = AuthGroup::getRoles();
        $this->assign('role', $roles[$user['role_id']]);
        $this->assign("user", $user);
        return $this->fetch();
    }

    /**
     * 显示修改密码
     */
    public function repassword()
    {
        return $this->fetch();
    }

    /**
     * 执行修改密码
     */
    public function doRepassword()
    {
        $post = Request::post();
        $user = \think\facade\Session::get("user");
        $user = \app\common\model\User::get($user['id']);
        $post['password'] = md5($post['password']);
        if ($user['password'] != $post['password']) {
            return json(['code'=>'500', 'msg'=>'请输入原密码']);
        }
        $user->password = md5($post['newpassword']);
        $result = $user->save();

        if($result) {
            return json(['code'=>'200', 'msg'=>'修改密码成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'修改密码失败']);
        }
    }
}
