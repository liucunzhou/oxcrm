<?php
namespace app\h5\controller\ucenter;

use Firebase\JWT\JWT;
use app\common\model\User;
use think\Controller;

class Passport extends Controller
{
    public function dologin()
    {
        // nickname,password
        $params = $this->request->param();

        $where = [];
        $where[] = ['nickname', '=', $params['nickname']];

        $field = 'id,nickname,realname,mobile,password';
        $user = User::field($field)->where($where)->find();
        if($user->password != md5($params['password'])) {
            $arr = [
                'code'  => '400',
                'msg'   => '用户名或者密码错误'
            ];

            return $arr;
        }

        $salt = 'hongsi';
        $data = $user->getData();
        unset($data['password']);

        $token = JWT::encode($data, $salt);
        $arr = [
            'code'  => '200',
            'msg'   => '获取token成功',
            'data'  => [
                'token' =>  $token,
                'user'  =>  $user
            ]
        ];

        return json($arr);
    }
}