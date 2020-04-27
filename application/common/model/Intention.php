<?php
namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

class Intention extends Model
{
    protected $pk = 'id';
    // protected $autoWriteTimestamp = 'datetime';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function getIntentions($update=false)
    {
        $data = self::order('is_valid desc,sort desc,id asc')->column('id,title,color,is_valid', 'id');
        $item =  [
            0 => [
                'id'    => 0,
                'title' => '未跟进',
                'is_valid' => 1
            ]
        ];

        array_unshift($data, $item);
        return $data;
    }

    /**
     * 获取清洗组意向
     */
    public static function getWash($withAll=true)
    {
        $map = [];
        $map[]      = ['type', 'in', ['wash', 'other']];
        $fields     = 'id,title,type';
        $rows       = self::where($map)->field($fields)->order('sort,id')->column($fields, 'id');
        $unvisit    = ['id' => 0, 'title' => '未跟进', 'type' => 'wash'];
        array_unshift($rows, $unvisit);

        if($withAll) {
            $all = ['id' => -1, 'title' => '全部客资', 'type' => 'wash'];
            array_unshift($rows, $all);
        }

        return $rows;
    }

    public static function getIntentionsByRole($roleId=0)
    {
        $data = self::getIntentions();
        return $data;
    }

    ### 更新缓存
    public static function updateCache()
    {
        self::getIntentions(true);
    }
}