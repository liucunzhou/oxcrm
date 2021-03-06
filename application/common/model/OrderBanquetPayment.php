<?php
namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

class OrderBanquetPayment extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    use \think\model\concern\SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    protected $type = [
        'banquet_apply_pay_date' => 'timestamp',
        'banquet_pay_real_date' => 'timestamp',
        'sign_date' => 'timestamp',
        'event_date' => 'timestamp'
    ];
}