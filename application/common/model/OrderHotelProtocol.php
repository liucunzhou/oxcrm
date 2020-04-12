<?php
namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

class OrderHotelProtocol extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    protected $type = [
        'earnest_money_date' => 'timestamp',
        'middle_money_date' => 'timestamp',
        'tail_money_date' => 'timestamp',
    ];
}