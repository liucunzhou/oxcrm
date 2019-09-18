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
        $request = Request::param();
        if (!empty($request['create_time'])) {
            $range = $this->getDateRange($request['create_time']);

            ### 客资总数
            $map = [];
            $map[] = ['user_id', '=', $this->user['id']];
            $map[] = ['create_time', 'between', $range];
            // print_r($map);
            $total[0] = MemberAllocate::where($map)->count();

            ### 未跟进统计
            $map = [];
            $map[] = ['active_status', '=', 0];
            $map[] = ['user_id', '=', $this->user['id']];
            $map[] = ['create_time', 'between', $range];
            $total[1] = MemberAllocate::where($map)->count();

            ### 跟进中
            $map = [];
            $map[] = ['active_status', '=', 1];
            $map[] = ['user_id', '=', $this->user['id']];
            $map[] = ['create_time', 'between', $range];
            $total[2] = MemberAllocate::where($map)->count();

            ### 有效客资
            $map = [];
            $map[] = ['active_status', '=', 5];
            $map[] = ['user_id', '=', $this->user['id']];
            $map[] = ['create_time', 'between', $range];
            $total[3] = MemberAllocate::where($map)->count();

            ### 有效客资
            $map = [];
            $map[] = ['active_status', '=', 5];
            $map[] = ['user_id', '=', $this->user['id']];
            $map[] = ['create_time', 'between', $range];
            $total[3] = MemberAllocate::where($map)->count();

            ### 意向客户
            $map = [];
            $map[] = ['active_status', '=', 6];
            $map[] = ['user_id', '=', $this->user['id']];
            $map[] = ['create_time', 'between', $range];
            $total[4] = MemberAllocate::where($map)->count();

            ### 无效客户
            $map = [];
            $map[] = ['active_status', '=', 4];
            $map[] = ['user_id', '=', $this->user['id']];
            $map[] = ['create_time', 'between', $range];
            $total[5] = MemberAllocate::where($map)->count();

            ### 失效客户
            $map = [];
            $map[] = ['active_status', '=', 3];
            $map[] = ['user_id', '=', $this->user['id']];
            $map[] = ['create_time', 'between', $range];
            $total[6] = MemberAllocate::where($map)->count();

            ### 进店客户
            $map = [];
            $map[] = ['is_into_store', '=', 1];
            $map[] = ['user_id', '=', $this->user['id']];
            $map[] = ['create_time', 'between', $range];
            $total[7] = MemberAllocate::where($map)->count();

            ### 成单客户
            $map = [];
            $map[] = ['active_status', '=', 2];
            $map[] = ['user_id', '=', $this->user['id']];
            $map[] = ['create_time', 'between', $range];
            $total[8] = MemberAllocate::where($map)->count();
            ### 重单
            // $map = [];
            // $map[] = ['repeat_log', '<>', ''];
            // $map[] = ['user_id', 'in', $userIds];
            // $total[7] = MemberAllocate::where($map)->count();
            $this->assign('total', $total);

            // 统计有效
            $map = [];
            $map[] = ['user_id', '=', $this->user['id']];
            $map[] = ['source_text', '<>', ''];
            $map[] = ['create_time', 'between', $range];
            $result = MemberAllocate::where($map)->field('source_text,active_status,count(*) as amount')->group('source_text,active_status')->select();
            $groupSource = [];
            $totalSum = 0;
            $totalsArr = [];
            $totalsArr0 = [];
            $totalsArr1 = [];
            $totalsArr2 = [];
            $totalsArr3 = [];
            $totalsArr4 = [];
            $totalsArr5 = [];
            $totalsArr6 = [];
            if (!empty($result)) {
                $groupData = $result->toArray();
                foreach ($groupData as $key => $value) {
                    $k = $value['source_text'];
                    $activeStatus = $value['active_status'];
                    $groupSource[$k][$activeStatus] = $value['amount'];
                }

                $totalsArr = [];
                foreach ($groupSource as &$group) {
                    $total = (int)$group[0] + (int)$group[1] + (int)$group[5] + (int)$group[6] + (int)$group[3] + (int)$group[4] + $group[2];
                    $group[100] = $total;

                    $totalsArr[] = $total;
                    $totalsArr0[] = $group[0];
                    $totalsArr1[] = $group[1];
                    $totalsArr2[] = $group[2];
                    $totalsArr3[] = $group[3];
                    $totalsArr4[] = $group[4];
                    $totalsArr5[] = $group[5];
                    $totalsArr6[] = $group[6];
                }
            }

            // 统计有效
            $map = [];
            $map[] = ['user_id', '=', $this->user['id']];
            $map[] = ['is_into_store', '=', 1];
            $map[] = ['source_text', '<>', ''];
            $map[] = ['create_time', 'between', $range];
            $result = MemberAllocate::where($map)->field('source_text,is_into_store,count(*) as amount')->group('source_text,is_into_store')->select();
            $totalsArr99 = [];
            if (!empty($result)) {
                $groupData = $result->toArray();
                foreach ($groupData as $key => $value) {
                    $k = $value['source_text'];
                    $groupSource[$k][99] = $value['amount'];
                    $totalsArr99[] = $value['amount'];
                }
            }

            $totalSum = array_sum($totalsArr);
            $groupSource['总计'][100] = $totalSum;

            $totalSum99 = array_sum($totalsArr99);
            $groupSource['总计'][99] = $totalSum99;

            $totalSum0 = array_sum($totalsArr0);
            $groupSource['总计'][0] = $totalSum0;

            $totalSum1 = array_sum($totalsArr1);
            $groupSource['总计'][1] = $totalSum1;

            $totalSum2 = array_sum($totalsArr2);
            $groupSource['总计'][2] = $totalSum2;

            $totalSum3 = array_sum($totalsArr3);
            $groupSource['总计'][3] = $totalSum3;

            $totalSum4 = array_sum($totalsArr4);
            $groupSource['总计'][4] = $totalSum4;

            $totalSum5 = array_sum($totalsArr5);
            $groupSource['总计'][5] = $totalSum5;

            $totalSum6 = array_sum($totalsArr6);
            $groupSource['总计'][6] = $totalSum6;


            $this->assign('groupSource', $groupSource);
        }

        $this->assign('get', $request);
        return $this->fetch();
    }

    public function storeManager()
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
                $map[] = ['create_time', 'between', $range];
                // print_r($map);
                $total[0] = MemberAllocate::where($map)->count();

                ### 未跟进统计
                $map = [];
                $map[] = ['active_status', '=', 0];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
                $total[1] = MemberAllocate::where($map)->count();

                ### 跟进中
                $map = [];
                $map[] = ['active_status', '=', 1];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
                $total[2] = MemberAllocate::where($map)->count();

                ### 有效客资
                $map = [];
                $map[] = ['active_status', '=', 5];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
                $total[3] = MemberAllocate::where($map)->count();

                ### 有效客资
                $map = [];
                $map[] = ['active_status', '=', 5];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
                $total[3] = MemberAllocate::where($map)->count();

                ### 意向客户
                $map = [];
                $map[] = ['active_status', '=', 6];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
                $total[4] = MemberAllocate::where($map)->count();

                ### 无效客户
                $map = [];
                $map[] = ['active_status', '=', 4];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
                $total[5] = MemberAllocate::where($map)->count();

                ### 失效客户
                $map = [];
                $map[] = ['active_status', '=', 3];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
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
            $map[] = ['create_time', 'between', $range];
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


    public function storeAreaManager()
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
                $map[] = ['create_time', 'between', $range];
                // print_r($map);
                $total[0] = MemberAllocate::where($map)->count();

                ### 未跟进统计
                $map = [];
                $map[] = ['active_status', '=', 0];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
                $total[1] = MemberAllocate::where($map)->count();

                ### 跟进中
                $map = [];
                $map[] = ['active_status', '=', 1];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
                $total[2] = MemberAllocate::where($map)->count();

                ### 有效客资
                $map = [];
                $map[] = ['active_status', '=', 5];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
                $total[3] = MemberAllocate::where($map)->count();

                ### 有效客资
                $map = [];
                $map[] = ['active_status', '=', 5];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
                $total[3] = MemberAllocate::where($map)->count();

                ### 意向客户
                $map = [];
                $map[] = ['active_status', '=', 6];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
                $total[4] = MemberAllocate::where($map)->count();

                ### 无效客户
                $map = [];
                $map[] = ['active_status', '=', 4];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
                $total[5] = MemberAllocate::where($map)->count();

                ### 失效客户
                $map = [];
                $map[] = ['active_status', '=', 3];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
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
            $map[] = ['create_time', 'between', $range];
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