<?php
namespace app\index\model;

use think\Model;
use think\model\concern\SoftDelete;

class MemberAllocate extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public function member()
    {

        return $this->belongsTo('member', 'member_id');
    }
}