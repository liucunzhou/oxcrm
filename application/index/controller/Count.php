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

        $request = Request::param();
        if (empty($request['dimension']) || $request['dimension'] == 'source') {
            $dimension = 'source';
            $this->assign('dimension', '渠道');
        } else {
            $dimension = 'staff';
            $this->assign('dimension', '员工');
        }

        if ($request['department_id']) {

            $userIds = User::getUsersByDepartmentId($request['department_id']);
            if (!empty($userIds) && !empty($request['create_time'])) {
                $range = $this->getDateRange($request['create_time']);

                ### 客资总数
                $map = [];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
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

                $map = [];
                $map[] = ['source_text', '<>', ''];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
                if ($dimension == 'source') {
                    $result = MemberAllocate::where($map)->field('source_text,active_status,count(*) as amount')->group('source_text,active_status')->select();
                    if (!empty($result)) {
                        $groupSource = [];
                        $groupData = $result->toArray();
                        foreach ($groupData as $key => $value) {
                            $k = $value['source_text'];
                            $activeStatus = $value['active_status'];
                            $groupSource[$k][$activeStatus] = $value['amount'];
                            $groupSource[$k]['text'] = $value['source_text'];
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
                } else {
                    $users = User::getUsers();
                    $result = MemberAllocate::where($map)->field('user_id,active_status,count(*) as amount')->group('user_id,active_status')->select();
                    if (!empty($result)) {
                        $groupSource = [];
                        $groupData = $result->toArray();
                        foreach ($groupData as $key => $value) {
                            $userId = $value['user_id'];
                            $activeStatus = $value['active_status'];
                            $groupSource[$userId][$activeStatus] = $value['amount'];
                            $groupSource[$userId]['text'] = $users[$value['user_id']]['realname'];
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
            }
        }

        $this->assign('request', $request);
        return $this->fetch();
    }


    public function storeAreaManager()
    {
        $departments = Department::getDepartments();
        $this->assign('departments', $departments);

        $request = Request::param();

        if (empty($request['dimension']) || $request['dimension'] == 'source') {
            $dimension = 'source';
            $this->assign('dimension', '渠道');
        } else {
            $dimension = 'staff';
            $this->assign('dimension', '员工');
        }

        if ($request['department_id']) {

            $userIds = User::getUsersByDepartmentId($request['department_id']);
            if (!empty($userIds) && !empty($request['create_time'])) {
                $range = $this->getDateRange($request['create_time']);

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


                $map = [];
                $map[] = ['source_text', '<>', ''];
                $map[] = ['user_id', 'in', $userIds];
                $map[] = ['create_time', 'between', $range];
                if ($dimension == 'source') {
                    $this->groupBySource($request['department_id'], $range);
                    /**
                     * $result = MemberAllocate::where($map)->field('source_text,active_status,count(*) as amount')->group('source_text,active_status')->select();
                     * if (!empty($result)) {
                     * $groupSource = [];
                     * $groupData = $result->toArray();
                     * foreach ($groupData as $key => $value) {
                     * $k = $value['source_text'];
                     * $activeStatus = $value['active_status'];
                     * $groupSource[$k][$activeStatus] = $value['amount'];
                     * $groupSource[$k]['text'] = $value['source_text'];
                     * }
                     *
                     * $totalsArr = [];
                     * foreach ($groupSource as &$group) {
                     * $total = (int)$group[0] + (int)$group[1] + (int)$group[5] + (int)$group[6] + (int)$group[3] + (int)$group[4];
                     * $group[100] = $total;
                     * $totalsArr[] = $total;
                     * }
                     *
                     * $totalSum = array_sum($totalsArr);
                     * $groupSource['总计'][100] = $totalSum;
                     * $this->assign('groupSource', $groupSource);
                     * }
                     **/
                } else {
                    $users = User::getUsers();
                    $result = MemberAllocate::where($map)->field('user_id,active_status,count(*) as amount')->group('user_id,active_status')->select();
                    if (!empty($result)) {
                        $groupSource = [];
                        $groupData = $result->toArray();
                        foreach ($groupData as $key => $value) {
                            $userId = $value['user_id'];
                            $activeStatus = $value['active_status'];
                            $groupSource[$userId][$activeStatus] = $value['amount'];
                            $groupSource[$userId]['text'] = $users[$value['user_id']]['realname'];
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
            }

        }

        $this->assign('request', $request);
        return $this->fetch();
    }

    public function countStaffSource()
    {
        $request = Request::param();
        $range = $this->getDateRange($request['create_time']);
        $map = [];
        $map[] = ['source_text', '<>', ''];
        $map[] = ['user_id', '=', $request['user_id']];
        $map[] = ['create_time', 'between', $range];
        $result = MemberAllocate::where($map)->field('source_text,active_status,count(*) as amount')->group('source_text,active_status')->select();
        if (!empty($result)) {
            $groupSource = [];
            $groupData = $result->toArray();
            foreach ($groupData as $key => $value) {
                $k = $value['source_text'];
                $activeStatus = $value['active_status'];
                $groupSource[$k][$activeStatus] = $value['amount'];
                $groupSource[$k]['text'] = $value['source_text'];
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

        return $this->fetch();
    }

    private function groupBySource($departmentId, $dateRange)
    {
        $userIds = User::getUsersByDepartmentId($departmentId);
        $map[] = ['source_text', '<>', ''];
        $map[] = ['user_id', 'in', $userIds];
        $map[] = ['create_time', 'between', $$dateRange];
        ### 获取所有去除重复的客资
        $mobiles = MemberAllocate::where($map)->field('mobile')->group('mobile')->select();
        var_dump($mobiles);
        $data = [];
        $totals = count($mobiles);
        foreach ($mobiles as $row) {
            $mobile = $row->mobile;
            $allocateObj = new MemberAllocate();
            $where = [];
            $where[] = ['mobile', '=', $mobile];
            $where[] = ['user_id', 'in', $userIds];
            $allocates = $allocateObj->where($where)->select();
            foreach ($allocates as $allocate) {
                $activeStatus = $allocate->active_status;
                // 成单客户
                if ($activeStatus == 2) {
                    continue;
                }

                // 有效客户
                if ($activeStatus == 5) {
                    continue;
                }

                // 意向客户
                if ($activeStatus == 6) {
                    continue;
                }

                // 跟进中
                if ($activeStatus == 1) {
                    continue;
                }

                // 未跟进
                if ($activeStatus == 0) {
                    continue;
                }

                // 无效客户
                if ($activeStatus == 4) {
                    continue;
                }

                // 失效客户
                if ($activeStatus == 3) {
                    continue;
                }
            }
        }
    }

    private function getDateRange($dateRange)
    {
        if ($dateRange == 'today') {

            $start = strtotime(date('Y-m-d'));
            $end = strtotime('tomorrow');

        } else {

            $range = explode('~', $dateRange);
            $range[0] = str_replace("+", "", trim($range[0]));
            $range[1] = str_replace("+", "", trim($range[1]));
            $start = strtotime($range[0]);
            $end = strtotime($range[1]) + 86400;
        }

        return [$start, $end];
    }
}