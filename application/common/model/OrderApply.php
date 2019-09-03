<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class OrderApply extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public function orderEntire()
    {
        return $this->belongsTo('OrderEntire', 'order_id');
    }
}