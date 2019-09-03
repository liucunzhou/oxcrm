<?php
namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

// 预算
class Budget extends Model
{
    protected $pk = 'id';
    // protected $autoWriteTimestamp = 'datetime';
    protected $autoWriteTimestamp = true;

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function getBudgetList($update=false)
    {
        $cacheKey = 'budgets';
        $data = redis()->get($cacheKey);
        $data = json_decode($data, true);
        if(empty($data) || $update) {
            // $map[] = ['parent_id', '=', 0];
            $data = self::order('is_valid desc,sort desc,id asc')->column('id,title,is_valid', 'id');
            $json = json_encode($data);
            $result = redis()->set($cacheKey, $json);
        }
        return $data;
    }

    public static function getBudget($id,$update=false)
    {
        $data = self::get($id);
        return $data;
    }

    public static function updateCache()
    {
        self::getBudgetList(true);

        self::getBudget(true);
    }
}