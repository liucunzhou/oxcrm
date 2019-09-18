<?php
namespace app\common\model;

use app\api\model\Member;
use think\Model;
use think\model\concern\SoftDelete;

class MemberAllocate extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public function member()
    {
        return $this->belongsTo('member', 'member_id');
    }

    public static function getAllocate($userId, $memberId) {
        $map = [];
        $map[] = ['user_id', '=', $userId];
        $map[] = ['member_id', '=', $memberId];

        $data = self::where($map)->find();
        return $data;
    }

    public static function insertAllocateData($userId, $memberId, $data)
    {
        unset($data['id']);
        unset($data['create_time']);
        $data['user_id'] = $userId;
        $data['member_id'] = $memberId;

        $map = [];
        $map[] = ['user_id', '=', $userId];
        $map[] = ['member_id', '=', $memberId];
        $allocate = self::where($map)->find();
        if($allocate) {
            $result = $allocate->allowField(true)->save($data);
        } else {
            $member = Member::get($memberId);
            $data['member_create_time'] = strtotime($member->create_time);
            $data['update_time'] = 0;
            $data['create_time'] = time();
            $MemberAllocate = new MemberAllocate();
            $result = $MemberAllocate->allowField(true)->save($data);
        }

        return $result;
    }

    public static function updateAllocateData($userId, $memberId, $data)
    {
        unset($data['id']);
        unset($data['create_time']);
        $data['user_id'] = $userId;
        $data['member_id'] = $memberId;

        $map = [];
        $map[] = ['user_id', '=', $userId];
        $map[] = ['member_id', '=', $memberId];
        $allocate = self::where($map)->find();
        if($allocate) {
            $member = Member::get($memberId);
            $data['member_create_time'] = strtotime($member->create_time);
            $result = $allocate->allowField(true)->save($data);
        } else {
            // $data['update_time'] = 0;
            // $data['create_time'] = time();
            // $MemberAllocate = new MemberAllocate();
            // $result = $MemberAllocate->allowField(true)->save($data);
            $result = 1;
        }

        return $result;
    }

    public static function searchAllocateData($userId, $memberId, $data) {
        unset($data['id']);
        unset($data['create_time']);
        $data['user_id'] = $userId;
        $data['member_id'] = $memberId;

        $map = [];
        $map[] = ['user_id', '=', $userId];
        $map[] = ['member_id', '=', $memberId];
        $allocate = self::where($map)->find();
        if($allocate) {
            $data = [];
            $member = Member::get($memberId);
            $data['modify_time'] = time();
            $data['member_create_time'] = strtotime($member->create_time);
            unset($data['active_status']);
            $result = $allocate->allowField(true)->save($data);
        } else {
            $member = Member::get($memberId);
            $data['member_create_time'] = strtotime($member->create_time);
            $data['update_time'] = 0;
            $data['create_time'] = time();
            $MemberAllocate = new MemberAllocate();
            $result = $MemberAllocate->allowField(true)->save($data);
        }

        return $result;
    }
}