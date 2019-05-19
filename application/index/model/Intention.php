<?php
namespace app\index\model;


use think\Model;
use think\model\concern\SoftDelete;

class Intention extends Model
{
    protected $pk = 'id';
    // protected $autoWriteTimestamp = 'datetime';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function getIntentions($update=false)
    {
        $cacheKey = 'intentions';
        $data = redis()->get($cacheKey);
        if(empty($data) || $update) {
            $data = self::order('is_valid desc,sort desc,id asc')->column('id,title,is_valid', 'id');
            $json = json_encode($data);
            $result = redis()->set($cacheKey, $json);
        } else {

            $data = json_decode($data, true);
        }

        return $data;
    }

    ### 更新缓存
    public static function updateCache()
    {

    }
}