<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Auth extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function getAuth($id, $update=false)
    {
        $cacheKey = 'auth';
        $data = redis()->hGet($cacheKey, $id);
        $data = json_decode($data, true);
        if(empty($data) || $update) {
            $map[] = ['id', '=', $id];
            $data = self::where($map)->field('id,route')->find()->toArray();
            $json = json_encode($data);
            $result = redis()->hSet($cacheKey, $id,$json);
        }

        return $data;
    }

    public static function getModules($update=false)
    {
        $cacheKey = 'auth_modules';
        $data = redis()->get($cacheKey);
        $data = json_decode($data, true);
        if(empty($data) || $update) {
            $map[] = ['parent_id', '=', 0];
            $data = self::where($map)->order('is_valid desc,sort desc,id asc')->column('id,parent_id,title,route', 'id');
            $json = json_encode($data);
            $result = redis()->set($cacheKey, $json);
        }


        return $data;
    }

    public static function getList($update=false)
    {
        $cacheKey = 'auth_items';
        $data = redis()->get($cacheKey);
        $data = json_decode($data, true);
        if(empty($data) || $update) {
            $data = self::order('parent_id,sort desc')->column('id,parent_id,title,route,is_menu', 'id');
            $json = json_encode($data);
            $result = redis()->set($cacheKey, $json);
        }

        return $data;
    }

    public static function getMenuList()
    {
        $nlist = [];
        $list = self::getList();
        foreach ($list as $value) {
            if($value['is_menu'] != 1) continue;
            $nlist[] = $value;
        }

        return $nlist;
    }

    public static function updateCache($id)
    {
        self::getAuth($id, true);
        self::getModules(true);
        self::getList(true);
    }
}