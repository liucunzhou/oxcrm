<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class MobileRelation extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function getMobiles($mobile) {
        $obj = self::getByMobile($mobile);
        $mobiles = [];
        if(!empty($obj)) {
            $data = $obj->getData();
            if(!empty($data['mobiles'])) {
                $mobiles = explode(',', $data['mobiles']);
            }
        }

        return $mobiles;
    }

    public static function getLikeMobiles($mobile) {
        $map = [];
        $map[] = ['mobile', 'like', "%{$mobile}%"];
        $data = self::where($map)->column('mobile');

        return $data;
    }
}