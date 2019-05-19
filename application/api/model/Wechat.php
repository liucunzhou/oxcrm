<?php
namespace app\api\model;


class Wechat
{
    protected static $host = 'https://api.weixin.qq.com';
    protected static $appid = 'wx069835e6aa56a379';
    protected static $secret = 'b4816b9d1d4ef1b4adfe79d36862bbdb';

    public function login($code)
    {
        $url = self::$host.'/sns/jscode2session?appid='.self::$appid.'&secret='.self::$secret.'&js_code='.$code.'&grant_type=authorization_code';
        $loginRs = file_get_contents($url);
	file_put_contents('1.txt', $loginRs);
        return json_decode($loginRs, true);
    }
}
