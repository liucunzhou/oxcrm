<?php

namespace app\wash\controller\count;

use app\wash\controller\Backend;

class Ring extends Backend
{

    protected $staffs = [];

    protected function initialize()
    {
        parent::initialize();
        $departmentId = $this->user['department_id'];
        $this->staffs = \app\common\model\User::getUsersInfoByDepartmentId($departmentId, false);
        $this->assign('staffs', $this->staffs);
    }

    // 通话统计
    public function call()
    {
        $params = $this->request->param();
        if (!empty($params['range']) && strpos($params['range'], '~') > 0) {
            $arr = explode('~', $params['range']);
            $range = [trim($arr[0]), trim($arr[1])];
        } else {
            $range = [];
        }

        if(!empty($params['staff'])) {
            if(count($params['staff']) > 1) {
                $staffCondition = ['userid', 'in', $params['staff']];
            } else {
                $staffCondition = ['userid', '=', $params['staff'][0]];
            }
        } else {
            $staffCondition = [];
        }

        $users = \app\common\model\User::getUsers();
        $this->assign('users', $users);

        $callRecord = new \app\common\model\CallRecord();

        // 拨打总数
        $where = [];
        $where[] = ['userid', '>', 0];
        !empty($range) && $where[] = ['fwdStartTime', 'between', $range];
        !empty($staffCondition) && $where[] = $staffCondition;
        $totals = $callRecord->field('userid, count(*) as amount')
            ->where($where)
            ->group('userid')
            ->select();

        // 接通总数
        $where = [];
        $where[] = ['userid', '>', 0];
        !empty($range) && $where[] = ['fwdStartTime', 'between', $range];
        !empty($staffCondition) && $where[] = $staffCondition;
        $result = $callRecord->field('userid, count(*) as amount')
            ->where($where)
            ->whereNotNull('fwdAnswerTime')
            ->group('userid')
            ->select();
        if (!empty($result)) {
            $success = $result->toArray();
            $success = array_column($success, 'amount', 'userid');
        } else {
            $success = [];
        }


        // 总时长
        $where = [];
        $where[] = ['userid', '>', 0];
        !empty($range) && $where[] = ['fwdStartTime', 'between', $range];
        !empty($staffCondition) && $where[] = $staffCondition;
        $result = $callRecord->field('userid, sum(billsec) as seconds')
            ->where($where)
            ->group('userid')
            ->select();
        if ($result) {
            $seconds = $result->toArray();
            $seconds = array_column($seconds, 'seconds', 'userid');
        } else {
            $seconds = [];
        }

        // 合并出结果
        $list = [];
        foreach ($totals as $row) {
            $userId = $row->userid;
            $amount = $row->amount;
            $successAmount = isset($success[$userId]) ? $success[$userId] : 0;
            $percent = round($successAmount * 100 / $amount, 2);
            $duration = isset($seconds[$userId]) ? $seconds[$userId] : 0;
            if($successAmount > 0) {
                $avg = $duration / $successAmount;
            } else {
                $avg = 0;
            }

            $list[] = [
                'id' => $userId,
                'totals' => $amount,
                'success' => $successAmount,
                'percent' => $percent,
                'seconds' => $this->formatSeconds($duration),
                'avg' => round($avg, 2)
            ];
        }
        $this->assign('list', $list);

        return $this->fetch();
    }

    private function formatSeconds($time)
    {
        $output = '';
        $units = [86400 => '天', 3600 => '小时', 60 => '分', 1 => '秒'];
        foreach ($units as $key => $value) {
            if ($time >= $key) $output .= floor($time / $key) . $value;
            $time %= $key;
        }


        if ($output == '') {
            $output = 0;
        }

        return $output;
    }
}