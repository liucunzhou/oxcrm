<?php

namespace app\index\controller;

use app\common\model\MemberAllocate;
use app\common\model\UploadCustomerLog;
use think\facade\Request;

class Wash extends Base
{

    protected $staffs = [];

    protected function initialize()
    {
        parent::initialize();
        $departmentId = $this->user['department_id'];
        $this->staffs = \app\common\model\User::getUsersInfoByDepartmentId($departmentId);
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
            $avg = $duration / $successAmount;

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

    /**
     * 上传统计
     */
    public function upload()
    {
        $params = $this->request->param();
        if (!empty($params['range']) && strpos($params['range'], '~') > 0) {
            $arr = explode('~', $params['range']);
            $start = strtotime(trim($arr[0]));
            $end = strtotime(trim($arr[1]));
            $range = [$start, $end];
        } else {
            $range = [];
        }

        $uploadLog = new UploadCustomerLog();
        // 总数
        $fields = "source_text,from_unixtime(create_time,'%Y%m%d') days,city_text,count(*) total";
        $groupFields = "source_text,days,city_text";

        $where = [];
        !empty($range) && $where[] = ['create_time', 'between', $range];
        $result = $uploadLog->field($fields)->where($where)->group($groupFields)->select();
        if (!empty($result)) {
            $totals = [];
            foreach ($result as $row) {
                $index = $row->source_text . '_' . $row->days . '_' . $row->city_text;
                $totals[$index] = $row->getData();
            }
        } else {
            $totals = [];
        }

        // 重复
        $where = [];
        !empty($range) && $where[] = ['create_time', 'between', $range];
        $result = $uploadLog->field($fields)->where($where)->whereNotNull('duplicate')->group($groupFields)->select();
        if (!empty($result)) {
            $repeat = [];
            foreach ($result as $row) {
                $index = $row->source_text . '_' . $row->days . '_' . $row->city_text;
                $repeat[$index] = $row->getData();
            }
        } else {
            $repeat = [];
        }

        // 有效的
        $where = [];
        !empty($range) && $where[] = ['create_time', 'between', $range];
        $result = $uploadLog->field($fields)->where($where)->whereNull('duplicate')->group($groupFields)->select();
        if (!empty($result)) {
            $success = [];
            foreach ($result as $row) {
                $index = $row->source_text . '_' . $row->days . '_' . $row->city_text;
                $success[$index] = $row->getData();
            }
        } else {
            $success = [];
        }

        $list = [];
        foreach ($totals as $key => $row) {
            $rtotal = $row['total'];
            $repeatTotal = isset($repeat[$key]) ? $repeat[$key]['total'] : 0;
            $successTotal = isset($success[$key]) ? $success[$key]['total'] : 0;
            $spercent = round($successTotal * 100 / $rtotal, 2);

            $list[$key] = [
                'source_text' => $row['source_text'],
                'days' => $row['days'],
                'city_text' => $row['city_text'],
                'total' => $rtotal,
                'repeat' => $repeatTotal,
                'success' => $successTotal,
                'spercent' => $spercent
            ];
        }
        $this->assign('list', $list);

        return $this->fetch();
    }

    public function repeat()
    {
        // $staffs = \app\common\model\User::getUsersByDepartmentId(10);
        $status = \app\common\model\Intention::getIntentions();

        $params = $this->request->param();
        $uploadLog = new UploadCustomerLog();

        $where = [];
        $where[] = ['source_text', '=', $params['source_text']];
        $where[] = ['city_text', '=', $params['city_text']];
        $start = strtotime($params['days']);
        $end = $start + 86400;
        $where[] = ['create_time', 'between', [$start, $end]];
        $list = $uploadLog->where($where)->whereNotNull('duplicate')->paginate(30);
        foreach ($list as &$row) {

            $map = [];
            $map[] = ['mobile', '=', $row->mobile];
           // $map[] = ['user_id', 'in', $staffs];
            $allocate = MemberAllocate::where($map)->field('active_status')->order('create_time')->find();
            $row->status = empty($allocate) ? '未找到' : $status[$allocate->active_status]['title'];
            $row->mobile = substr_replace($row->mobile, '***', 3, 3);
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