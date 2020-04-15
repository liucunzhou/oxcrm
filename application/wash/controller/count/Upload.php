<?php

namespace app\wash\controller\count;

use app\common\model\MemberAllocate;
use app\common\model\UploadCustomerLog;
use app\wash\controller\Backend;

class Upload extends Backend
{

    protected $staffs = [];

    protected function initialize()
    {
        parent::initialize();
        $departmentId = $this->user['department_id'];
        $this->staffs = \app\common\model\User::getUsersInfoByDepartmentId($departmentId, false);
        $this->assign('staffs', $this->staffs);
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
}