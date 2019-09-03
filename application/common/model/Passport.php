<?php
namespace app\common\model;


class Passport
{
    private $loginLimitTimes = 6;

    /**
     * 用户名、密码验证
     */
    public function checkUserPassword($args)
    {
        $where[] = ['nickname', '=', $args['nickname']];
        $user = User::where($where)->find();

        if(!empty($user)) {
            $user = $user->toArray();

            if ($user['password'] == md5($args['password'])) {
                $result = $user;
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * 检验当前IP登录的次数
     */
    public function checkIpLoginTimes()
    {

    }


}