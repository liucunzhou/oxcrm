<?php
namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

class OrderBanquet extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    // use \think\model\concern\SoftDelete;
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    protected $type = [
        'sign_date' => 'timestamp',
        'event_date' => 'timestamp'
    ];
}