<?php
namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

class OrderWeddingSuborder extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    use \think\model\concern\SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
}