<?php
namespace app\h5\controller\ucenter;


use app\common\model\AuthGroup;
use app\common\model\User;
use app\h5\controller\Base;

class Usercenter extends Base
{

    public $user = [];
    protected function initialize()
    {
        parent::initialize();
        // $this->auth = UserAuth::getUserLogicAuth($this->user['id']);
    }

    ###  个人信息
    public function index()
    {
        $param = $this->request->param();
        ###  个人信息
        $usersCenter = User::getUser($param['id']);
        ###  个人权限
        $data = AuthGroup::getAuthGroup($usersCenter['role_id']);
        $usersCenter['title'] = $data['title'];
        $usersCenter['auth_type'] = $data['auth_type'];
        $result = [
            'code'  =>  '200',
            'msg'   =>  '获取个人信息',
            'data'  =>  $usersCenter
        ];

        return json($result);
    }

    /**
     * 修改密码
     * Method doRepassword
     * @return \think\response\Json
     */
    public function doRepassword()
    {
        $param = $this->request->param();
        $user = User::get($param['id']);
        $post['password'] = md5($param['password']);

        if ($user['password'] != $post['password']) {
            return json(['code'=>'400', 'msg'=>'请输入原密码']);
        }
        $user->password = md5($param['newpassword']);
        $result = $user->save();

        if($result) {
            return json(['code'=>'200', 'msg'=>'修改密码成功']);
        } else {
            return json(['code'=>'400', 'msg'=>'修改密码失败']);
        }
    }
}