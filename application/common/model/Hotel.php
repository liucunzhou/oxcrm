<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Hotel extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function getHotels($update=false)
    {
        $cacheKey = 'Hotel';
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

    public static function updateCache()
    {
        self::getHotels(true);
    }
}