<?php
namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

class OrderWeddingPayment extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    protected $type = [
        'wedding_apply_pay_date' => 'timestamp',
        'sign_date' => 'timestamp',
        'event_date' => 'timestamp'
    ];
}