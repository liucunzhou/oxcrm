<?php
namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

class Order extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    protected $type = [
        'earnest_money_date' => 'timestamp',
        'middle_money_date' => 'timestamp',
        'tail_money_date' => 'timestamp',
        'sign_date' => 'timestamp',
        'event_date' => 'timestamp'
    ];


}