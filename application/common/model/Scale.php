<?php
namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

// 桌数
class Scale extends Model
{
    protected $pk = 'id';
    // protected $autoWriteTimestamp = 'datetime';
    protected $autoWriteTimestamp = true;

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function getScaleList($update=false)
    {
        $cacheKey = 'scales';
        $data = redis()->get($cacheKey);
        $data = json_decode($data, true);
        if(empty($data) || $update) {
            // $map[] = ['parent_id', '=', 0];
            $data = self::order('is_valid desc,sort desc,id asc')->column('id,title,is_valid', 'id');
            // echo self::getLastSql();
            $json = json_encode($data);
            $result = redis()->set($cacheKey, $json);
        }

        return $data;
    }

    public static function getScale($id, $update=false)
    {
        $data = self::get($id);

        return $data;
    }

    public static function updateCache()
    {
        self::getScaleList(true);

        self::getScale(true);
    }
}