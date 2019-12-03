<?php
namespace app\common\model;


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
        return $data;
    }

    public static function getDepartmentTree($departmentId)
    {
        $department = self::getDepartment($departmentId);
        $map = [];
        $map[] = ['path', 'like', "{$department['path']}%"];
        $departments = self::where($map)->column('id,parent_id,title,depth', 'id');

        return $departments;
    }

    public static function getTree($departmentId)
    {
        $department = self::getDepartment($departmentId);
        $map = [];
        $map[] = ['path', 'like', "{$department['path']}%"];
        $departments = self::where($map)->column('id');

        return $departments;
    }

    ### 更新缓存
    public static function updateCache()
    {
        self::getDepartments(true);
    }
}