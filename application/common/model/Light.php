<?php

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Light extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function getList($update=false)
    {
        $data = self::order('is_valid desc,sort desc,id asc')->column('id,title,is_valid', 'id');
        return $data;
    }

    public static function updateCache()
    {
        self::getList(true);
    }
}
