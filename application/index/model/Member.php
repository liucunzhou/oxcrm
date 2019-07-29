<?php
namespace app\index\model;


use think\Model;
use think\model\concern\SoftDelete;

class Member extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    /**
     * 获取电话号码对应的用户ID
     * @param $mobile
     * @return mixed
     */
    public static function checkMobile($mobile)
    {
        $hashKey = "mobiles";
        $data = redis()->hGet($hashKey, $mobile);
        return $data;
    }

    /***
     * 电话号码列表
     * @param $mobile
     * @param $memberId
     */
    public static function pushMoblie($mobile, $memberId)
    {
        $hashKey = "mobiles";
        redis()->hSet($hashKey, $mobile, $memberId);
    }

    /***
     * 更新客资缓存
     */
    public static function updateCache()
    {

    }
}