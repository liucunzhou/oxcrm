<?php

namespace app\common\model;

use think\Model;

class OrderCar extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    protected $type = [
        'arrive_time' => 'timestamp',
    ];
}
