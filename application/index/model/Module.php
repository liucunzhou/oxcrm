<?php
namespace app\index\model;

use think\Model;
use think\model\concern\SoftDelete;

class Module extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function getList()
    {
        $list = self::column('id,name');

        return $list;
    }

    public static function getModules()
    {
        $list = self::column("id,name,sort");

        return $list;
    }
}