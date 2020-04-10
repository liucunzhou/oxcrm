<?php
namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

class OrderHotelItem extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

}