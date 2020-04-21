<?php

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class MemberEnter extends Model
{
    protected $pk = 'id';
    // protected $autoWriteTimestamp = false;
    protected $autoWriteTimestamp = true;

    ### 软删除设置
    use SoftDelete;

    protected $type = [
        'subscribe_time' => 'timestamp',
        'real_time' => 'timestamp',
        'next_time' => 'timestamp'
    ];

}