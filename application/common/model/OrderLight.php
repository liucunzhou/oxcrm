<?php

namespace app\common\model;

use think\Model;

class OrderLight extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    use \think\model\concern\SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
}
