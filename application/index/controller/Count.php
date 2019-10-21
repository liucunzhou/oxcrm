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
            if(!empty($request['member_create_time'])) {
                $range = $this->getDateRange($request['member_create_time']);
            } else if(!empty($request['create_time'])) {
                $range = $this->getDateRange($request['create_time']);
            } else {
                $today = date('Y-m-d');
                $range = $this->getDateRange($today);
            }

            if (!empty($userIds)) {
                if ($dimension == 'source') {
                    $group = $this->groupBySource($userIds, $range);
                } else {
                    $group = $this->groupByStaff($userIds, $range);
                    $users = User::getUsers();

                    $this->assign('users', $users);
                }

                $this->assign('group', $group);

                $totals = [];
                // 总数
                $totalsArr = array_column($group, 'total');
                $totals['total'] = array_sum($totalsArr);
                // 总未跟进
                $novisitArr = array_column($group, 'novisit');
                $totals['novisit'] = array_sum($novisitArr);
                // 总跟进中
                $visitingArr = array_column($group, 'visiting');
                $totals['visiting'] = array_sum($visitingArr);
                // 总有效
                $effectiveArr = array_column($group, 'effective');
                $totals['effective'] = array_sum($effectiveArr);
                // 总意向
                $possibleArr = array_column($group, 'possible');
                $totals['possible'] = array_sum($possibleArr);
                // 总无效
                $invalidArr = array_column($group, 'invalid');
                $totals['invalid'] = array_sum($invalidArr);
                // 总失效
                $loseArr = array_column($group, 'lose');
                $totals['lose'] = array_sum($loseArr);
                // 总进店
                $intoStoreArr = array_column($group, 'into_store');
                $totals['into_store'] = array_sum($intoStoreArr);
                // 总订单
                $orderArr = array_column($group, 'order');
                $totals['order'] = array_sum($orderArr);
                $this->assign('totals', $totals);
            }

        }

        $request['create_time'] = str_replace('+', '', $request['create_time']);
        $request['member_create_time'] = str_replace('+', '', $request['member_create_time']);
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

    private function groupByStaff($userIds, $dateRange)
    {
        $map = [];
        // $map[] = ['source_text', '<>', ''];
        $map[] = ['user_id', 'in', $userIds];
        $map[] = ['create_time', 'between', $dateRange];
        $group = MemberAllocate::where($map)->field('user_id,active_status,count(*) as amount')->group('user_id,active_status')->select();
        // echo MemberAllocate::getLastSql();
        $data = [];
        foreach ($group as $key => $value) {
            $userId = $value['user_id'];
            $activeStatus = $value['active_status'];
            $amount = $value['amount'];
            // 设置未跟进
            if($activeStatus == 0) {
                if(!isset($data[$userId]['novisit'])) {
                    $data[$userId]['novisit'] = $amount;
                } else {
                    $data[$userId]['novisit'] = $data[$userId]['novisit'] + $amount;
                }
            }

            // 设置跟进中
            if($activeStatus == 1) {
                if(!isset($data[$userId]['visiting'])) {
                    $data[$userId]['visiting'] = $amount;
                } else {
                    $data[$userId]['visiting'] = $data[$userId]['visiting'] + $amount;
                }
            }

            // 有效
            if($activeStatus == 5) {
                if(!isset($data[$userId]['effective'])) {
                    $data[$userId]['effective'] = $amount;
                } else {
                    $data[$userId]['effective'] = $data[$userId]['effective'] + $amount;
                }
            }

            // 可能有效
            if($activeStatus == 6) {
                if(!isset($data[$userId]['possible'])) {
                    $data[$userId]['possible'] = $amount;
                } else {
                    $data[$userId]['possible'] = $data[$userId]['possible'] + $amount;
                }
            }

            // 无效
            if($activeStatus == 4) {
                if(!isset($data[$userId]['invalid'])) {
                    $data[$userId]['invalid'] = $amount;
                } else {
                    $data[$userId]['invalid'] = $data[$userId]['invalid'] + $amount;
                }
            }

            // 失效
            if($activeStatus == 3) {
                if(!isset($data[$userId]['lose'])) {
                    $data[$userId]['lose'] = $amount;
                } else {
                    $data[$userId]['lose'] = $data[$userId]['lose'] + $amount;
                }
            }

        }

        $map = [];
        // $map[] = ['source_text', '<>', ''];
        $map[] = ['is_into_store', '=', 1];
        $map[] = ['create_time', 'between', $dateRange];
        $group = MemberAllocate::where($map)->where('mobile', 'in', function ($query) use ($userIds, $dateRange) {
            $map = [];
            // $map[] = ['source_text', '<>', ''];
            $map[] = ['user_id', 'in', $userIds];
            $map[] = ['create_time', 'between', $dateRange];
            $query->table('tk_member_allocate')->where($map)->field('mobile')->group('mobile');
        })->field('operate_id,count(*) as amount')->group('operate_id')->select();
        foreach ($group as $key => $value) {
            $userId = $value['operate_id'];
            if(!in_array($userId, $userIds)) continue;
            $amount = $value['amount'];
            // 设置未跟进
            if(!isset($data[$userId]['into_store'])) {
                $data[$userId]['into_store'] = $amount;
            } else {
                $data[$userId]['into_store'] = $data[$userId]['into_store'] + $amount;
            }
        }

        $map = [];
        // $map[] = ['source_text', '<>', ''];
        $map[] = ['active_status', '=', 2];
        $map[] = ['create_time', 'between', $dateRange];
        $group = MemberAllocate::where($map)->where('mobile', 'in', function ($query) use ($userIds, $dateRange) {
            $map = [];
            // $map[] = ['source_text', '<>', ''];
            $map[] = ['user_id', 'in', $userIds];
            $map[] = ['create_time', 'between', $dateRange];
            $query->table('tk_member_allocate')->where($map)->field('mobile')->group('mobile');
        })->field('operate_id,count(*) as amount')->group('operate_id')->select();

        foreach ($group as $key => $value) {
            $userId = $value['operate_id'];
            if(!in_array($userId, $userIds)) continue;

            $amount = $value['amount'];
            // 设置未跟进
            if(!isset($data[$userId]['order'])) {
                $data[$userId]['order'] = $amount;
            } else {
                $data[$userId]['order'] = $data[$userId]['order'] + $amount;
            }
        }

        foreach ($data as $key=>$value) {
            $data[$key]['total'] = $value['effective'] + $value['possible'] + $value['visiting'] + $value['novisit'] + $value['invalid'] + $value['lose'];
        }
        return $data;
    }

    private function groupBySource($userIds, $dateRange)
    {
        // $map[] = ['source_text', '<>', ''];
        $map[] = ['user_id', 'in', $userIds];
        $map[] = ['create_time', 'between', $dateRange];
        ### 获取所有去除重复的客资
        $result = MemberAllocate::field('source_text,mobile,user_id,active_status,is_into_store')->where('mobile', 'in', function ($query) use ($map) {
            $query->table('tk_member_allocate')->where($map)->field('mobile')->group('mobile');
        })->select();

        $group = [];
        foreach ($result as $row) {
            $source = $row->source_text;
            $mobile = $row->mobile;
            $activeStatus = $row->active_status;
            $isIntoStore = $row->is_into_store;
            $userId = $row->user_id;
            if($isIntoStore > 0) {
                $group[$source][$mobile]['is_into_store'] = 1;
            }

            if($activeStatus == 2) {
                $group[$source][$mobile]['is_order'] = 1;
            }

            if(in_array($userId, $userIds)) {
                $group[$source][$mobile]['active_status'][] = $activeStatus;
            }
        }

        $data = [];
        foreach ($group as $key=>$mobiles) {
            foreach ($mobiles as $value) {

                // 统计进店
                if (isset($value['is_into_store'])) {
                    if (isset($data[$key]['into_store'])) {
                        $data[$key]['into_store'] = $data[$key]['into_store'] + 1;
                    } else {
                        $data[$key]['into_store'] = 1;
                    }
                }

                // 统计成单
                if (isset($value['is_order'])) {
                    if (isset($data[$key]['order'])) {
                        $data[$key]['order'] = $data[$key]['order'] + 1;
                    } else {
                        $data[$key]['order'] = 1;
                    }
                }

                // 统计状态
                if(isset($value['active_status'])) {
                    $activeStatusArr = $value['active_status'];
                    if (in_array(5, $activeStatusArr)) { // 统计有效
                        if (isset($data[$key]['effective'])) {
                            $data[$key]['effective'] = $data[$key]['effective'] + 1;
                        } else {
                            $data[$key]['effective'] = 1;
                        }
                    } else if (in_array(6, $activeStatusArr)) { // 统计可能有效
                        if (isset($data[$key]['possible'])) {
                            $data[$key]['possible'] = $data[$key]['possible'] + 1;
                        } else {
                            $data[$key]['possible'] = 1;
                        }
                    } else if (in_array(1, $activeStatusArr)) { // 统计跟进中
                        if (isset($data[$key]['visiting'])) {
                            $data[$key]['visiting'] = $data[$key]['visiting'] + 1;
                        } else {
                            $data[$key]['visiting'] = 1;
                        }
                    } else if (in_array(0, $activeStatusArr)) { // 统计未跟进
                        if (isset($data[$key]['novisit'])) {
                            $data[$key]['novisit'] = $data[$key]['novisit'] + 1;
                        } else {
                            $data[$key]['novisit'] = 1;
                        }
                    } else if (in_array(2, $activeStatusArr)) { // 统计无效

                        continue;

                    } else if (in_array(4, $activeStatusArr)) { // 统计无效
                        if (isset($data[$key]['invalid'])) {
                            $data[$key]['invalid'] = $data[$key]['invalid'] + 1;
                        } else {
                            $data[$key]['invalid'] = 1;
                        }
                    } else if (in_array(3, $activeStatusArr)) { // 统计失效
                        if (isset($data[$key]['lose'])) {
                            $data[$key]['lose'] = $data[$key]['lose'] + 1;
                        } else {
                            $data[$key]['lose'] = 1;
                        }
                    }
                }
            }
        }

        foreach ($data as $key=>$value) {
            $data[$key]['total'] = $value['effective'] + $value['possible'] + $value['visiting'] + $value['novisit'] + $value['invalid'] + $value['lose'];
        }
        return $data;
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