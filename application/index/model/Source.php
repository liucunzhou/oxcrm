<?php
namespace app\index\model;


use think\Model;
use think\model\concern\SoftDelete;

class Source extends Model
{
    protected $pk = 'id';
    // protected $autoWriteTimestamp = 'datetime';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function getPlatforms($update=false)
    {
        $cacheKey = 'platform';
        $data = redis()->get($cacheKey);
        $data = json_decode($data, true);
        if(empty($data) || $update) {
            $map[] = ['parent_id', '=', 0];
            $data = self::where($map)->order('is_valid desc,sort desc,id asc')->column('id,parent_id,title,alias', 'id');
            // echo self::getLastSql();
            $json = json_encode($data);
            $result = redis()->set($cacheKey, $json);
        }

        return $data;
    }

    public static function getSources($update=false)
    {
        $cacheKey = 'source';
        $data = redis()->get($cacheKey);
        $data = json_decode($data, true);
        if(empty($data) || $update) {
            $map[] = ['parent_id', '>', 0];
            $data = self::where($map)->order('is_valid desc,sort desc,id asc')->column('id,parent_id,title,alias', 'id');
            $json = json_encode($data);
            $result = redis()->set($cacheKey, $json);
        }

        return $data;
    }

    public static function getSouresGroupByPlatform()
    {
        $platforms = self::getPlatforms();
        $sources = self::getSources();

        foreach ($platforms as $key=>&$platform) {
            foreach ($sources as $source) {
                if($source['parent_id']==$key) $platform['sources'][] = $source;
            }
        }

        return $platforms;
    }

    public static function updateCache()
    {
        self::getPlatforms(true);

        self::getSources(true);
    }
}