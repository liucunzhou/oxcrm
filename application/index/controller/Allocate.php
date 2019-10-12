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
        /**
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
        **/


        return $this->fetch();
    }

    public function uploadAllocate()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            $config = [
                'page' => $get['page']
            ];
            $list = model('UploadCustomerLog')->order('create_time desc')->paginate($get['limit'], false, $config);
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'pages' => $list->lastPage(),
                'count' => $list->total(),
                'data' => $list->getCollection()
            ];

            return json($result);
        } else {
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
            $realname = mb_convert_encoding($realname, 'UTF-8', ['Unicode','ASCII','GB2312','GBK','UTF-8','ISO-8859-1']);
            $sourceText = trim($row[2]);
            $sourceText = mb_convert_encoding($sourceText, 'UTF-8', ['Unicode','ASCII','GB2312','GBK','UTF-8','ISO-8859-1']);
            $cityName = trim($row[3]);
            $cityName = mb_convert_encoding($cityName, 'UTF-8', ['Unicode','ASCII','GB2312','GBK','UTF-8','ISO-8859-1']);

            $row[1] = trim($row[1]);
            $row[1] = preg_replace("/\s(?=\s)/", "", $row[1]);
            $row[1] = preg_replace("/^[(\xc2\xa0)|\s]+/", "", $row[1]);
            $row[1] = preg_replace("/[\n\r\t]/", ' ', $row[1]);
            $originMember = Member::checkPatchMobile($row[1]);
            if(!empty($originMember)) continue;
            $sourceId = $sources[$sourceText];
            if (empty($sourceId)) {
                return json([
                    'code' => '500',
                    'msg' => '请确认来源是否存在'
                ]);
            }

            $data = [];
            $data['member_no'] = date('YmdHis') . rand(100, 999);
            $data['realname'] = $realname;
            $data['mobile'] = $row[1];
            $data['source_id'] = $sources[$sourceText];
            $data['source_text'] = $sourceText;
            $data['city_id'] = $cities[$cityName];
            $data['operate_id'] = $this->user['id'];
            $data['create_time'] = $time;

            $MemberModel = new Member();
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
                $data['member_create_time'] = $member[$start]['create_time'];
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
        $redis->hMset($hashKey, ['download_repeat'=>1]);

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
     * 推荐组分配到派单组
     * @return mixed
     */
    public function assignRecommendToDispatchGroup()
    {
        $users = User::getUsers(false);
        foreach ($users as $key => $value) {
            if (!in_array($value['role_id'], [10, 11])) {
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
    public function doAssignRecommendToDispatchGroup()
    {
        $post = Request::post();
        $ids = explode(',', $post['ids']);
        if (empty($ids)) {
            return json([
                'code' => '500',
                'msg' => '请选择要分配的客资'
            ]);
        }

        $status = 1;
        $totals = count($ids);
        $success = 0;
        foreach ($ids as $id) {
            if (empty($id)) continue;
            $result = $this->executeAllocateToStaff($id, $post['staff'], $status);
            if($result) $success = $success + 1;
        }

        $fail = $totals - $success;
        return json([
            'code' => '200',
            'msg' => "分配到洗单组:成功{$success}个,失败{$fail}个"
        ]);
    }

    ###
    public function assignToMerchantGroup()
    {
        $users = User::getUsers(false);
        foreach ($users as $key => $value) {
            if ($value['role_id'] !=9 ) {
                unset($users[$key]);
            }
        }
        $this->assign('users', $users);
        return $this->fetch();
    }

    /***
     * 派单组分配到派单组视图
     * @return mixed
     */
    public function assignToDispatchGroup()
    {
        $users = User::getUsers(false);
        foreach ($users as $key => $value) {
            if (!in_array($value['role_id'], [10, 11])) {
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
     * 分配到派单组逻辑
     * @return \think\response\Json
     */
    public function doAssignToDispatchStaff()
    {
        $post = Request::post();
        $ids = explode(',', $post['ids']);
        if (empty($ids)) {
            return json([
                'code' => '500',
                'msg' => '请选择要分配的客资'
            ]);
        }

        $dispatchStaffIds = [];
        $users = User::getUsers(false);
        foreach ($users as $key => $value) {
            if (in_array($value['role_id'], [10, 11])) {
                // unset($users[$key]);
                $dispatchStaffIds[] = $value['id'];
            }
        }

        $totals = count($ids);
        $success = 0;
        foreach ($ids as $id) {
            if (empty($id)) continue;
            $result = $this->executeAllocateToDispatch($id, $post['staff'], $dispatchStaffIds);
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

    public function assignToWeddingGroup()
    {
        $users = User::getUsers(false);
        foreach ($users as $key => $value) {
            if (!in_array($value['role_id'], [27, 28])) {
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

    public function getMobileCustomer()
    {
        $request = Request::param();
        if(empty($request['ids'])) {
            $response = [
                'code' => 500,
                'msg' => '请选择客资'
            ];

            return json($response);
        }

        $allocate = MemberAllocate::getAllocate($this->user['id'], $request['ids']);
        if($allocate) {
            $response = [
                'code' => 501,
                'msg' => '您已拥有此客资，请勿重复领取'
            ];
            return $response;
        }

        $member = Member::get($request['ids']);
        if (!empty($member)) {
            $data = $member->getData();
            if($this->user['role_id'] == 10 || $this->user['role_id'] == 11) {
                $data['acitve_status'] = 1;
            } else {
                $data['acitve_status'] = 0;
            }
            $data['allocate_type'] = 1;
            $result = MemberAllocate::searchAllocateData($this->user['id'], $member->id, $data);
            $memberId = $member->id;
        }

        if ($result) {
            $response = [
                'code' => 200,
                'msg' => '获取成功',
                'count' => 0,
                'data' => $memberId
            ];
        } else {
            $response = [
                'code' => -1,
                'msg' => '获取客资失败',
                'count' => 0,
                'data' => []
            ];
        }

        return json($response);
    }

    private function executeAllocateToStaff($id, $staff, $status=0)
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
        $data['active_status'] = $status;
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
        $data['is_into_store'] = 0;
        $data['assign_status'] = 1;
        $allocate->save($data);

        if($result1) {
            $user = User::get($staff);
            if(!empty($user['dingding'])) {
                $users[] = $user['dingding'];
                $DingModel = new \app\api\model\DingTalk();
                $message = $DingModel->linkMessage("新增客资", "新增客资消息", "http://h5.hongsizg.com/pages/customer/mine?status=0&is_into_store=0&page_title=%E6%9C%AA%E8%B7%9F%E8%BF%9B%E5%AE%A2%E8%B5%84&time=".time());
                $DingModel->sendJobMessage($users, $message);
            }

            ### 记录log日志
            OperateLog::appendTo($allocate);
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * f
     * @param $id
     * @param $staff
     * @return bool
     */
    private function executeAllocateToDispatch($id, $staff, $dispatchStaffIds)
    {
        $allocate = MemberAllocate::get($id);
        if (!$allocate) false;

        ### 检查该用户是否已经分配过
        $isAllocated = MemberAllocate::getAllocate($staff, $allocate->member_id);
        if($isAllocated) return false;

        ### 删除其它分配组员的分配
        $where = [];
        $where[] = ['member_id', '=', $allocate->member_id];
        $where[] = ['user_id', 'in', $dispatchStaffIds];
        $allocateGroup = new MemberAllocate();
        $allocateGroup->save(['delete_time'=>time()], $where);

        ### 分配到指定员工
        $data = $allocate->getData();
        unset($data['id']);
        $data['operate_id'] = $this->user['id'];
        $data['user_id'] = $staff;
        $data['update_time'] = 0;
        $data['create_time'] = time();
        ### 分配后重新回访，修改回访状态
        $data['active_status'] = 1;
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
        $data['is_into_store'] = 0;
        $data['assign_status'] = 1;
        $allocate->save($data);

        if($result1) {
            $user = User::get($staff);
            if(!empty($user['dingding'])) {
                $users[] = $user['dingding'];
                $DingModel = new \app\api\model\DingTalk();
                $message = $DingModel->linkMessage("新增客资", "新增客资消息", "http://h5.hongsizg.com/pages/customer/mine?status=0&is_into_store=0&page_title=%E6%9C%AA%E8%B7%9F%E8%BF%9B%E5%AE%A2%E8%B5%84&time=".time());
                $DingModel->sendJobMessage($users, $message);
            }

            ### 记录log日志
            OperateLog::appendTo($allocate);
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    public function assignFromDispatchSeaToDispatchStaff()
    {
        $users = User::getUsers(false);
        foreach ($users as $key => $value) {
            if (!in_array($value['role_id'], [10, 11])) {
                unset($users[$key]);
            }
        }
        $this->assign('users', $users);
        return $this->fetch();
    }

    public function doAssignFromDispatchSeaToDispatchStaff()
    {
        $post = Request::post();
        $ids = explode(',', $post['ids']);
        if (empty($ids)) {
            return json([
                'code' => '500',
                'msg' => '请选择要分配的客资'
            ]);
        }

        $dispatchStaffIds = [];
        $users = User::getUsers(false);
        foreach ($users as $key => $value) {
            if (in_array($value['role_id'], [10, 11])) {
                // unset($users[$key]);
                $dispatchStaffIds[] = $value['id'];
            }
        }

        $totals = count($ids);
        $success = 0;
        foreach ($ids as $id) {
            if (empty($id)) continue;
            $result = $this->executeMembertoStaff($id, $post['staff'], $dispatchStaffIds);
            if($result) $success = $success + 1;
        }

        $fail = $totals - $success;
        return json([
            'code' => '200',
            'msg' => "分配到洗单组:成功{$success}个,失败{$fail}个"
        ]);
    }

    // 从派单组公海分配到门市销售
    public function assignFromDispatchSeaToStoreStaff()
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

    public function doAssignFromDispatchSeaToStoreStaff()
    {
        $post = Request::post();
        $ids = explode(',', $post['ids']);
        if (empty($ids)) {
            return json([
                'code' => '500',
                'msg' => '请选择要分配门店员工'
            ]);
        }

        $staffs = $post['staff'];
        foreach ($staffs as $staff) {
            foreach ($ids as $id) {
                if (empty($id)) continue;
                $result = $this->executeMembertoStoreStaff($id, $staff);
            }
        }
        return json([
            'code' => '200',
            'msg' => "分配到推荐组:成功~"
        ]);
    }

    /**
     * 分配到非派单组
     * @param $id
     * @param $staff
     * @return bool
     */
    private function executeMembertoStaff($id, $staff, $dispatchStaffIds)
    {

        ### 检查该用户是否已经分配过
        $isAllocated = MemberAllocate::getAllocate($staff, $id);
        if($isAllocated) return false;
        ### 删除其它分配组员的分配
        $where = [];
        $where[] = ['member_id', '=', $id];
        $where[] = ['user_id', 'in', $dispatchStaffIds];
        $allocateGroup = new MemberAllocate();
        $allocateGroup->save(['delete_time'=>time()], $where);

        $member = Member::get($id);
        $data = $member->getData();
        $data['operate_id'] = $this->user['id'];
        $data['user_id'] = $staff;
        $data['member_id'] = $data['id'];
        // 设置时间信息
        $data['update_time'] = 0;
        $data['create_time'] = time();
        $data['member_create_time'] = strtotime($member->create_time);
        ### 分配后重新回访
        $data['active_status'] = 1;
        $data['assign_status'] = 0;
        $data['active_assign_status'] = 0;
        $data['possible_assign_status'] = 0;
        unset($data['id']);
        $MemberAllocate = new MemberAllocate();
        $result1 = $MemberAllocate->allowField(true)->save($data);
        ### 标记已分配
        $member->save(['dispatch_assign_status'=>1,'dispatch_id'=>$staff]);

        if($result1) {
            $user = User::get($staff);
            if(!empty($user['dingding'])) {
                $users[] = $user['dingding'];
                $DingModel = new \app\api\model\DingTalk();
                $message = $DingModel->linkMessage("新增客资", "新增客资消息", "http://h5.hongsizg.com/pages/customer/mine?status=1&is_into_store=0&page_title=%E6%9C%AA%E8%B7%9F%E8%BF%9B%E5%AE%A2%E8%B5%84&time=".time());
                $DingModel->sendJobMessage($users, $message);
            }

            ### 记录log日志
            OperateLog::appendTo($MemberAllocate);
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * 分配到非派单组
     * @param $id
     * @param $staff
     * @return bool
     */
    private function executeMembertoStoreStaff($id, $staff)
    {

        ### 检查该用户是否已经分配过
        $isAllocated = MemberAllocate::getAllocate($staff, $id);
        if($isAllocated) return false;

        $member = Member::get($id);
        $data = $member->getData();
        $data['operate_id'] = $this->user['id'];
        $data['user_id'] = $staff;
        $data['member_id'] = $data['id'];
        $data['update_time'] = 0;
        $data['create_time'] = time();
        $data['member_create_time'] = strtotime($member->create_time);
        ### 分配后重新回访
        $data['active_status'] = 1;
        $data['assign_status'] = 0;
        $data['active_assign_status'] = 0;
        $data['possible_assign_status'] = 0;
        unset($data['id']);
        $MemberAllocate = new MemberAllocate();
        $result1 = $MemberAllocate->allowField(true)->save($data);
        ### 标记已分配
        $member->save(['dispatch_assign_status'=>1]);

        if($result1) {
            $user = User::get($staff);
            if(!empty($user['dingding'])) {
                $users[] = $user['dingding'];
                $DingModel = new \app\api\model\DingTalk();
                $message = $DingModel->linkMessage("新增客资", "新增客资消息", "http://h5.hongsizg.com/pages/customer/mine?status=0&is_into_store=0&page_title=%E6%9C%AA%E8%B7%9F%E8%BF%9B%E5%AE%A2%E8%B5%84&time=".time());
                $DingModel->sendJobMessage($users, $message);
            }

            ### 记录log日志
            OperateLog::appendTo($MemberAllocate);
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }
}