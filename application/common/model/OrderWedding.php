<?php
namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

class OrderWedding extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    protected $type = [
        'sign_date' => 'timestamp',
        'event_date' => 'timestamp'
    ];
}