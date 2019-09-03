<?php

namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

class Image extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = 'datetime';
    protected $defaultSoftDelete = 0;

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    ### 更新缓存
    public static function updateCache()
    {

    }
}