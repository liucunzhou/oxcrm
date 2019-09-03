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


    public static function getMemberVisitList($user, $auth, $member)
    {
        $map = [];
        $self = false;
        if ($auth['show_visit_log']===0) {
            // 只能查看自己当前的回访记录
            $self = true;
            $map[] = ['clienter_no', '=', $user['user_no']];
            $map[] = ['member_no', '=', $member['member_no']];
        } else {
            $map[] = ['member_no', '=', $member['member_no']];
        }

        $intentions = Intention::getIntentions();
        $list = self::where($map)->order('create_time desc')->select();
        if (!empty($list)) {
            $visits = $data = $list->toArray();
            $count = [];
            foreach ($visits as &$value) {

                $memberId = $value['member_id'];
                $userId = $value['user_id'];
                $time = strtotime($value['create_time']);
                $value['create_time'] = date('y/m/d H:i', $time);
                if($self) {
                    $cuser = $user;
                    $value['user_id'] = $user['realname'];
                    $value['clienter_no'] = $user['user_no'];
                    $value['status'] = $intentions[$value['status']]['title'];
                } else {
                    $cuser = User::getUserByNo($value['clienter_no']);
                    $cuser = $cuser[$value['clienter_no']];
                    $value['user_id'] = $cuser['realname'];
                    $status = $intentions[$value['status']]['title'];
                    $value['status'] = $status;
                }

                if(isset($count[$userId])) {
                    $count[$userId]['visit_times'] = $count[$userId]['visit_times'] + 1;
                } else {
                    $allocate = Allocate::getAllocate($cuser, $memberId);
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

        return ['log'=>$visits, 'count'=>$count];
    }
}