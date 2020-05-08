<?php
namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

class OrderWeddingReceivables extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    use \think\model\concern\SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    protected $type = [
        'wedding_income_date' => 'timestamp',
        'wedding_income_real_date' => 'timestamp',
        'sign_date' => 'timestamp',
        'event_date' => 'timestamp'
    ];
}