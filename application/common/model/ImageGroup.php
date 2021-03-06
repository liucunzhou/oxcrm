<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class ImageGroup extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = 'datetime';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    ### 更新缓存
    public static function updateCache()
    {

    }
}