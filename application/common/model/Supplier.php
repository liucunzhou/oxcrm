<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Supplier extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function getList($update=false)
    {
        $cacheKey = 'Supplier';
        $data = redis()->get($cacheKey);
        if(empty($data) || $update) {
            $data = self::order('is_valid desc,sort desc,id asc')->column('id,title,is_valid,bank_company,bank_account,bank_name', 'id');
            $json = json_encode($data);
            $result = redis()->set($cacheKey, $json);
        } else {

            $data = json_decode($data, true);
        }

        return $data;
    }

    public static function updateCache()
    {
        self::getList(true);
    }
}