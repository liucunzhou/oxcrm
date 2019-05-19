<?php
namespace app\api\model;


use think\Model;

class News extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = 'datetime';
}