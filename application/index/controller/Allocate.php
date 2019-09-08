<?php
namespace app\index\controller;

use app\common\model\Csv;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\OperateLog;
use app\common\model\Region;
use app\common\model\User;
use app\common\model\UserAuth;
use app\common\model\Source;
use app\common\model\Store;
use app\common\model\Intention;
use app\common\model\Budget;
use app\common\model\Scale;
use think\facade\Request;
use think\response\Download;

class Allocate extends Base
{
    protected $newsTypes = ['婚宴信息', '婚庆信息', '一站式'];
    protected $washStatus = [];
    protected $hotels = [];
    protected $sources = [];
    protected $status = [];
    protected $auth = [];
    protected $budgets = [];
    protected $scales = [];

    protected function initialize()
    {
        parent::initialize();
        // 获取系统来源,酒店列表,意向状态
        $this->sources = Source::getSources();
        $this->hotels = Store::getStoreList();
        $this->status = Intention::getIntentions();
        $this->auth = UserAuth::getUserLogicAuth($this->user['id']);
        $this->budgets = Budget::getBudgetList();
        $this->scales = Scale::getScaleList();

        if (!Request::isAjax()) {
            $staffes = User::getUsersInfoByDepartmentId($this->user['department_id'], false);
            $this->assign('staffes', $staffes);
            $this->assign('sources', $this->sources);
        }
    }

    /**
     * 导入客资
     * @return mixed|\think\response\Json
     */
    public function import()
    {
        $hashKey = "batch_upload:" . $this->user['id'];
        if (!empty($_FILES)) {
            $file = request()->file("file");
            $info = $file->move("../uploads");
            if ($info) {
                $fileName = $info->getPathname();
                $data = Csv::readCsv($fileName);
                $this->assign('data', $data[0]);
                $cacheData = [
                    'file' => $fileName,
                    'amount' => count($data[0]),
                    'repeat' => count($data[1]),
                    'download_repeat' => 0
                ];
                redis()->hMset($hashKey, $cacheData);
                return json(['code' => '200', 'msg' => '上传成功,请继续分配', 'data' => $cacheData]);
            } else {
                return json(['code' => '500', 'msg' => '上传失败']);
            }
        }
        $fileData = redis()->hMget($hashKey, ['file', 'amount', 'repeat']);
        $this->assign('fileData', $fileData);

        $users = User::getUsers(false);
        foreach ($users as $key => $value) {
            $auth = UserAuth::getUserLogicAuth($value["id"]);
            $roleIds = explode(',', $auth['role_ids']);
            if (!in_array(7, $roleIds) && !in_array(2, $roleIds)) {
                unset($users[$key]);
            }
        }
        $this->assign('users', $users);

        return $this->fetch();
    }

    /**
     * 分配到洗单组主管逻辑
     * @return \think\response\Json
     */
    public function doUploadAllocate()
    {
        $post = Request::post();
        setlocale(LC_ALL, 'zh_CN');
        ### 获取文件信息
        $hashKey = "batch_upload:" . $this->user['id'];
        $fileData = redis()->hMGet($hashKey, ['file', 'amount', 'download_repeat']);
        if($fileData['download_repeat'] == 0) {
            return json(['code'=>'500','msg'=>'请先下载重复客资']);
        }

        if (empty($fileData['file']) || $fileData['amount'] == 0) {
            return json(['code' => '500', 'msg' => '没有要分配的客资']);
        }

        ### 获取分配信息
        $manager = [];
        foreach ($post as $key => $value) {
            if (strpos($key, 'user_') === 0 && $value > 0) {
                $id = substr($key, 5);
                $manager[$id] = $value;
            }
        }

        ### 检验分配数量
        $sum = array_sum($manager);
        if ($sum < $fileData['amount']) {
            return json(['code' => '500', 'msg' => '分配数量小于上传数量']);
        }
        if ($sum > $fileData['amount']) {
            return json(['code' => '500', 'msg' => '分配数量大于上传数量']);
        }

        $sources = Source::getSourcesIndexOfTitle();
        $cities = Region::getCityListIndexOfShortname();

        ### 开始分配
        $time = time();
        $result = Csv::readCsv($fileData['file']);
        $member = [];
        foreach ($result[0] as $key => $row) {
            if (empty($row)) continue;
            $realname = trim($row[0]);
            $realname = mb_convert_encoding($realname, 'UTF-8', 'GBK');
            $sourceText = trim($row[2]);
            $sourceText = mb_convert_encoding($sourceText, 'UTF-8', 'GBK');
            $cityName = trim($row[3]);
            $cityName = mb_convert_encoding($cityName, 'UTF-8', 'GBK');

            $row[1] = trim($row[1]);
            $row[1] = intval($row[1]);
            $originMember = Member::checkMobile($row[1]);
            if(!empty($originMember)) continue;

            $MemberModel = new Member();
            $data = [];
            $data['member_no'] = date('YmdHis') . rand(100, 999);
            $data['realname'] = $realname;
            $data['mobile'] = $row[1];
            $data['source_id'] = $sources[$sourceText];
            $data['source_text'] = $sourceText;
            $data['city_id'] = $cities[$cityName];
            $data['operate_id'] = $this->user['id'];
            $data['create_time'] = $time;
            $result = $MemberModel->insert($data);
            if ($result) {
                $data['member_id'] = $MemberModel->getLastInsID();
                $member[] = $data;
            }
        }

        $startArr = [];
        foreach ($manager as $k => $v) {
            $start = array_sum($startArr);
            $end = $start + $v;
            for ($start; $start < $end; $start++) {
                if (!isset($member[$start])) continue;
                $AllocateModel = new MemberAllocate();
                $data = $member[$start];
                $data['user_id'] = $k;
                $AllocateModel->insert($data);
            }
            $startArr[] = $v;
        }
         redis()->delete($hashKey);
        return json([
            'code' => '200',
            'msg' => '录入数据成功'
        ]);
    }

    /**
     * 导出重复客资
     */
    public function export()
    {
        ### 获取文件信息
        $redis = redis();
        $hashKey = "batch_upload:" . $this->user['id'];
        $fileData = $redis->hMGet($hashKey, ['file', 'amount']);
        if (empty($fileData['file'])) {
            return json(['code' => '500', 'msg' => '没有要分配的客资']);
        }

        // Csv::readCsv($fileData['file'])
        $file = $fileData['file'];
        $filename = basename($file);
        $dir = "../uploads/" . date('Ymd') . "/";
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        // echo "<pre>";
        $nfile = $dir . "dump_" . $filename;
        $nfile = Csv::exportCsv($file, $nfile);

        // exit;
        $download = new Download($nfile);
        $download->mimeType('csv');
        $download->expire(1);
        $redis->hMset($hashKey, ['download_repeat'=>1]);
        return $download->name($filename);
    }


    /***
     * 分配到洗单组视图
     * @return mixed
     */
    public function assignToWashGroup()
    {
        $users = User::getUsers(false);
        foreach ($users as $key => $value) {
            if (!in_array($value['role_id'], [2, 7])) {
                unset($users[$key]);
            }
        }
        $this->assign('users', $users);
        return $this->fetch();
    }

    /**
     * 分配到洗单组逻辑
     * @return \think\response\Json
     */
    public function doAssignToStaff()
    {
        $post = Request::post();
        $ids = explode(',', $post['ids']);
        if (empty($ids)) {
            return json([
                'code' => '500',
                'msg' => '请选择要分配的客资'
            ]);
        }

        $totals = count($ids);
        $success = 0;
        foreach ($ids as $id) {
            if (empty($id)) continue;
            $result = $this->executeAllocateToStaff($id, $post['staff']);
            if($result) $success = $success + 1;
        }

        $fail = $totals - $success;
        return json([
            'code' => '200',
            'msg' => "分配到洗单组:成功{$success}个,失败{$fail}个"
        ]);
    }

    /**
     * 分配到推荐组视图
     * @return mixed
     */
    public function assignToRecommendGroup()
    {
        $users = User::getUsers(false);
        foreach ($users as $key => $value) {
            if (!in_array($value['role_id'], [3, 4])) {
                unset($users[$key]);
            }
        }
        $this->assign('users', $users);
        return $this->fetch();
    }

    /**
     * 分配到门店客服视图
     * @return mixed
     */
    public function assignToStoreStaff()
    {
        $users = User::getUsers(false);
        $staffs = [];
        foreach ($users as $value) {
            if ($value['role_id'] == '5' || $value['role_id'] == '8' || $value['role_id'] == '6') {
                array_push($staffs, $value);
            }
        }
        $this->assign('staffs', $staffs);

        return $this->fetch();
    }

    public function doAssignToStoreStaff()
    {
        $post = Request::post();
        $ids = explode(',', $post['ids']);
        if (empty($ids)) {
            return json([
                'code' => '500',
                'msg' => '请选择要分配门店员工'
            ]);
        }

        // $totals = count($ids);
        // $success = 0;
        $staffs = $post['staff'];
        foreach ($staffs as $staff) {
            foreach ($ids as $id) {
                if (empty($id)) continue;
                $result = $this->executeAllocateToStaff($id, $staff);
            }
        }
        return json([
            'code' => '200',
            'msg' => "分配到推荐组:成功~"
        ]);
    }

    private function executeAllocateToStaff($id, $staff)
    {
        $allocate = MemberAllocate::get($id);
        if (!$allocate) false;

        ### 检查该用户是否已经分配过
        $isAllocated = MemberAllocate::getAllocate($staff, $allocate->member_id);
        if($isAllocated) return false;

        $data = $allocate->getData();
        unset($data['id']);
        $data['operate_id'] = $this->user['id'];
        $data['user_id'] = $staff;
        $data['update_time'] = 0;
        $data['create_time'] = time();
        ### 分配后重新回访
        $data['active_status'] = 0;
        $data['active_assign_status'] = 0;
        $data['possible_assign_status'] = 0;
        $MemberAllocate = new MemberAllocate();
        $result1 = $MemberAllocate->insert($data);
        // 更新分配状态
        $data = [];
        if($allocate->active_status == 5) {
            $data['active_assign_status'] = 1;
        }
        if($allocate->active_status == 6) {
            $data['possible_assign_status'] = 1;
        }
        $data['assign_status'] = 1;
        $allocate->save($data);

        if($result1) {
            ### 记录log日志
            OperateLog::appendTo($allocate);
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }
}