<?php
namespace app\index\model;


use think\Model;
use think\model\concern\SoftDelete;

class Department extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    // protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function getDepartment($id)
    {
        $map[] = ['id', '=', $id];
        $data = self::where($map)->find();

        return $data;
    }

    public static function getDepartments()
    {

        $data = self::order('path')->column('id,title,depth', 'id');
        // echo self::getLastSql();
        // print_r($data);

        return $data;
    }

    public static function getChildren($id)
    {
        $map[] = ['parent_id', '=', $id];
        $map[] = ['delete_time', '=', '0'];

        $data = self::where($map)->order('is_valid desc,sort desc,id asc')->column('id,title', 'id');
    }

    public static function getTree($root=0)
    {

    }

    ### 更新缓存
    public static function updateCache()
    {
        self::getDepartments(true);
    }
}