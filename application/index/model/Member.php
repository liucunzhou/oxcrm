<?php
namespace app\index\model;


use think\Model;
use think\model\concern\SoftDelete;

class Member extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;


    public static function checkMobile($mobile)
    {
        $hashKey = "mobiles";
        $data = redis()->hGet($hashKey, $mobile);

        return $data;
    }

    public static function pushMoblie($mobile)
    {
        $hashKey = "mobiles";
        redis()->hSet($hashKey, $mobile, 1);
    }

    ### 更新缓存
    public static function updateCache()
    {

    }
}