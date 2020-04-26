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

            return json($arr);
        }

        $salt = 'hongsi';
        $data = $user->getData();
        unset($data['password']);
        unset($user['password']);

        $token = JWT::encode($data, $salt);
//        $token = $this->getToken($data);
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

    public function getToken( $data ){
        $key = "hongsi";  //这里是自定义的一个随机字串，应该写在config文件中的，解密时也会用，相当    于加密中常用的 盐  salt
        $token = [
            "iss"=>"",  //签发者 可以为空
            "aud"=>"", //面象的用户，可以为空
            "iat" => time()-3600, //签发时间
            "nbf" => time()-3600, //在什么时候jwt开始生效  （这里表示生成100秒后才生效）
            "exp" => time()+30, //token 过期时间
            "uid" => $data //记录的userid的信息，这里是自已添加上去的，如果有其它信息，可以再添加数组的键值对
        ];
        $jwt = JWT::encode($token,$key,"HS256"); //根据参数生成了 token

        return $jwt;
    }

}