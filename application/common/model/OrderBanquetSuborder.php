<?php
namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

class OrderBanquetSuborder extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

}