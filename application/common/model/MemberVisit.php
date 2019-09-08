<?php
namespace app\common\model;

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

    public function member()
    {
        return $this->belongsTo('member', 'member_id');
    }

    public static function getMemberVisitCount($member)
    {
        $map = [];
        $map[] = ['member_id', '=', $member['id']];
        $allocate = MemberAllocate::where($map)->select();
        $count = [];

    }

    public static function getMemberVisitList($user, $auth, $member)
    {
        $self = false;
        $intentions = Intention::getIntentions();
        if ($auth['show_visit_log']===0) $self = true;

        $map = [];
        $map[] = ['member_id', '=', $member['id']];
        $list = self::where($map)->order('create_time desc')->select();
        $userIds = [];
        if (!empty($list)) {
            $visits = $data = $list->toArray();
            $count = [];
            foreach ($visits as &$value) {
                $memberId = $value['member_id'];
                $userId = $value['user_id'];
                $userIds[] = $userId;
                $time = strtotime($value['create_time']);
                $value['create_time'] = date('y/m/d H:i', $time);
                $cuser = User::getUserByNo($value['clienter_no']);
                $cuser = $cuser[$value['clienter_no']];
                $value['user_id'] = $cuser['realname'];
                $status = $intentions[$value['status']]['title'];
                $value['status'] = $status;

                if(empty($cuser)) continue;
                if(isset($count[$userId])) {
                    $count[$userId]['visit_times'] = $count[$userId]['visit_times'] + 1;
                } else {
                    $allocate = MemberAllocate::getAllocate($cuser['id'], $memberId);
                    $count[$userId]['user_id'] = $value['user_id'];
                    $count[$userId]['allocate_create_time'] = $allocate->create_time;
                    $count[$userId]['next_visit_time'] = date('Y-m-d H:i', $value['next_visit_time']);
                    $count[$userId]['visit_times'] = 1;
                }
            }
        } else {
            $visits = [];
            $count = [];
        }

        if($self) {
            foreach ($visits as $key => $val) {
                if($val['clienter_no'] != $user['user_no']) {
                    unset($visits[$key]);
                }
            }
            $visits = array_filter($visits);
        }

        if($userIds) {
            $userIds = array_unique($userIds);
            $map = [];
            $map[] = ['member_id', '=', $member['id']];
            $allocateRs = MemberAllocate::where($map)->select();
            if ($allocateRs) {
                $users = User::getUsers();
                foreach ($allocateRs as $allocate) {
                    $cuserId = $allocate->user_id;
                    if(!in_array($cuserId, $userIds)) {
                        $count[$cuserId]['user_id'] = $users[$cuserId]['realname'];
                        $count[$cuserId]['allocate_create_time'] = $allocate->create_time;
                        $count[$cuserId]['next_visit_time'] = '-';
                        $count[$cuserId]['visit_times'] = 0;
                    }
                }
            }
        }


        return ['log'=>$visits, 'count'=>$count];
    }
}