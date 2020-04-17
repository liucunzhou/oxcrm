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

    public function index()
    {
        $request = $this->request->param();
        $usersCenter = User::getUser($request['id']);
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
}