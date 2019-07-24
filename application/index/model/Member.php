<?php
namespace app\index\model;


use think\Model;
use think\model\concern\SoftDelete;

class Member extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public function addFromCsvRow($row, $adminId)
    {
        /**
        if(self::checkMobile($row[1])) {
            return false;
        }
        **/
        $data['member_no'] = date('YmdHis').rand(100,999);
        $data['realname'] = $row[0];
        $data['mobile'] = $row[1];
        $data['mobile1'] = $row[2];
        $data['admin_id'] = $adminId;
        $data['banquet_size'] = $row[3];
        $data['budget'] = $row[4];
        $data['is_valid'] = $row[5];
        $data['remark'] = $row[6];
        $data['wedding_date'] = $row[7];
        $data['zone'] = $row[8];
        $data['hotel_id'] = $row[9];

        $result = self::save($data);
        if($result) {
            return self::getLastInsID();
        } else {
            return $row;
        }
    }

    public static function checkMobile($mobile)
    {
        $hashKey = "mobiles";
        $data = redis()->hGet($hashKey, $mobile);

        return $data;
    }

    public static function pushMoblie($mobile, $memberId)
    {
        $hashKey = "mobiles";
        redis()->hSet($hashKey, $mobile, $memberId);
    }

    ### 更新缓存
    public static function updateCache()
    {

    }
}