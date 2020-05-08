<?php
namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

class OrderWeddingPayment extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    use \think\model\concern\SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    protected $type = [
        'wedding_apply_pay_date' => 'timestamp',
        'wedding_pay_real_date' => 'timestamp',
        'sign_date' => 'timestamp',
        'event_date' => 'timestamp'
    ];
}