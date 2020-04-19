<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Audit extends Model
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
        $data = self::order('is_valid desc,sort desc,id asc')->column('id,title,is_valid', 'id');
        return $data;
    }

}