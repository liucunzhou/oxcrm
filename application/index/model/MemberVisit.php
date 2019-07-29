<?php
namespace app\index\model;

use think\Model;
use think\model\concern\SoftDelete;

class MemberVisit extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;


    public static function getMemberVisitList($auth, $member_id)
    {
        $users = User::getUsers();
        $map = [];
        $self = false;
        if (!$auth['show_visit_log']) {
            // 只能查看自己当前的回访记录
            $self = true;
            $map[] = ['user_id', '=', $auth['user_id']];
            $map[] = ['member_id', '=', $member_id];
            $current = $users[$auth['user_id']]['realname'];
            $intentions = Intention::getIntentionsByRole($auth['role_ids']);
        } else {
            $map[] = ['member_id', '=', $member_id];
        }

        $list = self::where($map)->where($map)->select();
        if (!empty($list)) {
            $visits = $data = $list->toArray();
            foreach ($visits as &$value) {

                $time = strtotime($value['create_time']);
                $value['create_time'] = date('y/m/d H:i', $time);
                if($self) {
                    $value['user_id'] = $current;
                    $value['status'] = $intentions[$value['status']]['title'];
                } else {
                    $user = $users[$value['user_id']];
                    $value['user_id'] = $user['realname'];
                    $intentions = Intention::getIntentionsByRole($user['role_id']);
                    $value['status'] = $intentions[$value['status']]['title'];
                }
            }
        } else {
            $visits = [];
        }

        return $visits;
    }
}