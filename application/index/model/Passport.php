<?php
namespace app\index\model;


class Passport
{
    private $loginLimitTimes = 6;

    /**
     * 用户名、密码验证
     */
    public function checkUserPassword($args)
    {
        $where[] = ['nickname', '=', $args['nickname']];
        $user = model('User')->where($where)->find()->toArray();

        if($user['password'] == md5($args['password'])) {
            $result = true;
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