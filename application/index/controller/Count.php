<?php
namespace app\index\controller;

use app\common\model\Department;
use app\common\model\MemberAllocate;
use app\common\model\User;
use think\facade\Request;

class Count extends Base
{
    public function index()
    {
        $departments = Department::getDepartments();
        $this->assign('departments', $departments);

        $post = Request::param();
        if($post['department_id']) {

            $userIds = User::getUsersByDepartmentId($post['department_id']);
            if (!empty($userIds) && !empty($post['create_time'])) {
                $range = $this->getDateRange($post['create_time']);

                ### 客资总数
                $map = [];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['member_create_time', 'between', $range];
                // print_r($map);
                $total[0] = MemberAllocate::where($map)->count();

                ### 未跟进统计
                $map = [];
                $map[] = ['active_status', '=', 0];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['member_create_time', 'between', $range];
                $total[1] = MemberAllocate::where($map)->count();

                ### 跟进中
                $map = [];
                $map[] = ['active_status', '=', 1];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['member_create_time', 'between', $range];
                $total[2] = MemberAllocate::where($map)->count();

                ### 有效客资
                $map = [];
                $map[] = ['active_status', '=', 5];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['member_create_time', 'between', $range];
                $total[3] = MemberAllocate::where($map)->count();

                ### 有效客资
                $map = [];
                $map[] = ['active_status', '=', 5];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['member_create_time', 'between', $range];
                $total[3] = MemberAllocate::where($map)->count();

                ### 意向客户
                $map = [];
                $map[] = ['active_status', '=', 6];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['member_create_time', 'between', $range];
                $total[4] = MemberAllocate::where($map)->count();

                ### 无效客户
                $map = [];
                $map[] = ['active_status', '=', 4];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['member_create_time', 'between', $range];
                $total[5] = MemberAllocate::where($map)->count();

                ### 失效客户
                $map = [];
                $map[] = ['active_status', '=', 3];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['member_create_time', 'between', $range];
                $total[6] = MemberAllocate::where($map)->count();

                ### 重单
                // $map = [];
                // $map[] = ['repeat_log', '<>', ''];
                // $map[] = ['user_id', 'in', $userIds];
                // $total[7] = MemberAllocate::where($map)->count();

                $this->assign('total', $total);
            }

            $map = [];
            $map[] = ['source_text', '<>', ''];
            $map[] = ['user_id', 'in', $userIds];
            $map[] = ['member_create_time', 'between', $range];
            $result = MemberAllocate::where($map)->field('source_text,active_status,count(*) as amount')->group('source_text,active_status')->select();
            if(!empty($result)) {
                $groupSource = [];
                $groupData = $result->toArray();
                foreach ($groupData as $key=>$value) {
                    $k = $value['source_text'];
                    $activeStatus = $value['active_status'];
                    $groupSource[$k][$activeStatus] = $value['amount'];
                }

                $totalsArr = [];
                foreach ($groupSource as &$group) {
                    $total = (int)$group[0] + (int)$group[1] + (int)$group[5] + (int)$group[6] + (int)$group[3] + (int)$group[4];
                    $group[100] = $total;
                    $totalsArr[] = $total;
                }

                $totalSum = array_sum($totalsArr);
                $groupSource['总计'][100] = $totalSum;
                $this->assign('groupSource', $groupSource);
            }
        }

        return $this->fetch();
    }

    public function hour()
    {
        return $this->fetch();
    }

    public function compare()
    {
        return $this->fetch();
    }

    private function getDateRange($dateRange) {
        if($dateRange == 'today') {

            $start = strtotime(date('Y-m-d'));
            $end = strtotime('tomorrow');

        } else {

            $range = explode('~', $dateRange);
            $range[0] = trim($range[0]);
            $range[1] = trim($range[1]);
            $start = strtotime($range[0]);
            $end = strtotime($range[1]) + 86400;
        }

        return [$start, $end];
    }
}