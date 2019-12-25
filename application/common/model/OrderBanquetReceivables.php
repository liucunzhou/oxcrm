<?php
namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

class OrderBanquetReceivables extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    protected $type = [
        'banquet_income_date' => 'timestamp',
        'banquet_income_real_date' => 'timestamp',
        'sign_date' => 'timestamp',
        'event_date' => 'timestamp'
    ];
}