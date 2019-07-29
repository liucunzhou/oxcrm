<?php
namespace app\index\controller;

use app\index\model\Allocate;
use app\index\model\Csv;
use app\index\model\DispatchAllocate;
use app\index\model\DuplicateLog;
use app\index\model\Hotel;
use app\index\model\Intention;
use app\index\model\Member;
use app\index\model\MemberAllocate;
use app\index\model\MemberApply;
use app\index\model\MemberVisit;
use app\index\model\OperateLog;
use app\index\model\RecommendAllocate;
use app\index\model\Region;
use app\index\model\Search;
use app\index\model\Source;
use app\index\model\Store;
use app\index\model\StoreAllocate;
use app\index\model\Tab;
use app\index\model\User;
use app\index\model\UserAuth;
use think\facade\Request;
use think\facade\Session;
use think\response\Download;

class Customer extends Base
{
    protected $newsTypes = ['婚宴信息', '婚庆信息', '一站式'];
    protected $washStatus = [];
    protected $hotels = [];
    protected $sources = [];
    protected $status = [];
    protected $auth = [];

    protected function initialize()
    {
        parent::initialize();
        // 获取系统来源,酒店列表,意向状态
        $this->sources = Source::getSources();
        $this->hotels = Store::getStoreList();
        if (in_array($this->user['role_id'], [2, 7])) {
            // 洗单组回访
            $this->status = [
                ['title' => '未跟进'],
                ['title' => '有效客资'],
                ['title' => '无效客资'],
                ['title' => '跟进中']
            ];
        } else {
            $this->status = Intention::getIntentions();
        }
        $this->auth = UserAuth::getUserLogicAuth($this->user['id']);
    }

    public function index()
    {
        $get = Request::param();
        if (Request::isAjax()) {
            $config = [
                'page' => $get['page']
            ];

            $status = [
                ['title' => '未跟进'],
                ['title' => '有效客资'],
                ['title' => '无效客资'],
                ['title' => '跟进中']
            ];
            $search = Search::customerIndex($this->user, $get);
            $Model = $search['model'];
            $map = $search['map'];
            if(empty($Model) && empty($map)) {
                $result = [
                    'code' => 0,
                    'msg' => '未匹配到对应的角色',
                    'count' => 0,
                    'data' => []
                ];
                return json($result);
            }
            $list = $Model->where($map)->with('member')->paginate($get['limit'], false, $config);
            if (!empty($list)) {
                $data = $list->getCollection()->toArray();
                foreach ($data as &$value) {
                    $member = $value['member'];
                    unset($member['id']);
                    unset($value['member']);
                    $value = array_merge($value, $member);
                    $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                    $value['news_type'] = $this->newsTypes[$value['news_type']];
                    $value['hotel_id'] = $this->hotels[$value['hotel_id']]['title'];
                    $value['intention_status'] = $value['wash_status'] ? $status[$value['wash_status']]['title'] : "跟进中";
                    $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
                    if($this->auth['is_show_alias'] == '1') {
                        $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['alias'] : '未知';
                    } else {
                        $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : '未知';
                    }
                }
                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => $list->total(),
                    'data' => $data
                ];

            } else {
                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => 0,
                    'data' => []
                ];
            }

            return json($result);

        } else {

            $tabs = Tab::customerIndex($this->user, $get);
            $this->assign('tabs', $tabs);
            $this->assign('get', $get);

            return $this->fetch();
        }
    }

    /**
     * 我的客资
     * @return mixed|\think\response\Json
     */
    public function mine()
    {
        $get = Request::param();
        if (Request::isAjax()) {
            $get = Request::param();
            $config = [
                'page' => $get['page']
            ];
            $search = Search::customerMine($this->user, $get);
            $Model = $search['model'];
            $map = $search['map'];
            if(empty($Model) && empty($map)) {
                $result = [
                    'code' => 0,
                    'msg' => '未匹配到对应的角色',
                    'count' => 0,
                    'data' => []
                ];
                return json($result);
            }

            $status = Intention::getIntentions();
            isset($get['keywords']) && $get['keywords'] = trim($get['keywords']);
            if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
                $map = [];
                $map[] = ['mobile', '=', $get['keywords']];
                $list = $Model->hasWhere('member', $map)->with('member')->paginate($get['limit'], false, $config);
                $total = $list->total();
                if ($total) {
                    // 全号搜索、添加到自己的客资中
                }
            } else if (isset($get['keywords']) && strlen($get['keywords']) < 11) {
                $map = [];
                $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
                $list = $Model::hasWhere('member', $map)->with('member')->paginate($get['limit'], false, $config);
            } else if (isset($get['keywords']) && strlen($get['keywords']) > 11) {
                $map = [];
                $map[] = ['mobile', '=', $get['keywords']];
            } else {
                $list = $Model->where($map)->with('member')->paginate($get['limit'], false, $config);
            }

            if(!empty($list)) {
                $data = $list->getCollection()->toArray();
                foreach ($data as &$value) {
                    $member = $value['member'];
                    unset($member['id']);
                    unset($value['member']);
                    $value = array_merge($value, $member);
                    $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                    $value['news_type'] = $this->newsTypes[$value['news_type']];
                    $value['hotel_id'] = $this->hotels[$value['hotel_id']]['title'];
                    $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
                    // $value['intention_status'] = $value['intention_status'] ? $status[$value['intention_status']]['title'] : "跟进中";
                    if($this->auth['is_show_alias'] == '1') {
                        $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['alias'] : '未知';
                    } else {
                        $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : '未知';
                    }
                }
                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => $list->total(),
                    'data' => $data
                ];
            } else {
                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => 0,
                    'data' => []
                ];
            }
            return json($result);

        } else {
            $tabs = Tab::customerMine($this->user, $get);
            $this->assign('tabs', $tabs);
            return $this->fetch();
        }
    }

    /**
     * 客资公海
     * @return mixed
     */
    public function seas()
    {
        $get = Request::param();
        if (Request::isAjax()) {
            $config = [
                'page' => $get['page']
            ];
            $map[] = ['is_sea', '=', '1'];
            $list = model('Member')->paginate($get['limit'], false, $config);
            $data = $list->getCollection()->toArray();
            foreach ($data as &$value) {
                $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                $value['news_type'] = $this->newsTypes[$value['news_type']];
                $value['hotel_id'] = $this->hotels[$value['hotel_id']]['title'];
                $value['intention_status'] = $value['intention_status'] ? $this->status[$value['intention_status']] : "跟进中";
                $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
                if($this->auth['is_show_alias'] == '1') {
                    $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['alias'] : '未知';
                } else {
                    $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : '未知';
                }
            }
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $data
            ];
            return json($result);

        } else {
            return $this->fetch();
        }
    }

    /**
     * 导入客资
     * @return mixed|\think\response\Json
     */
    public function import()
    {
        $user = Session::get("user");
        $hashKey = "batch_upload:" . $user['id'];
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
                    'repeat' => count($data[1])
                ];
                redis()->hMset($hashKey, $cacheData);
                return json(['code' => '200', 'msg' => '上传成功,请继续分配', 'data' => $cacheData]);
            } else {
                return json(['code' => '500', 'msg' => '上传失败']);
            }
        }
        $fileData = redis()->hMget($hashKey, ['file', 'amount', 'repeat']);
        $this->assign('fileData', $fileData);

        $users = User::getUsers();
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
     * 导出重复客资
     */
    public function export()
    {
        // $down = new
        $hashKey = "batch_upload:";
        $download = new Download("dd");
        return $download->name('无效客资.csv');
    }

    /**
     * 分配到洗单组主管逻辑
     * @return \think\response\Json
     */
    public function doAllocate()
    {
        $post = Request::post();
        ### 获取文件信息
        $user = Session::get("user");
        $hashKey = "batch_upload:" . $user['id'];
        $operateId = $user['id'];
        $fileData = redis()->hMGet($hashKey, ['file', 'amount']);
        if (empty($fileData['file']) || $fileData['amount']==0) {
            return json(['code'=>'500', 'msg'=>'没有要分配的客资']);
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
            return json(['code'=>'500', 'msg'=>'分配数量小于上传数量']);
        }
        if ($sum > $fileData['amount']) {
            return json(['code'=>'500', 'msg'=>'分配数量大于上传数量']);
        }

        ### 开始分配
        $time = time();
        $result = Csv::readCsv($fileData['file']);

        $member = [];
        foreach ($result[0] as $key => $row) {
            if (empty($row)) continue;
            $MemberModel = new Member();
            $data = [];
            $data['member_no'] = date('YmdHis') . rand(100, 999);
            $data['realname'] = $row[0];
            $data['mobile'] = $row[1];
            $data['mobile1'] = $row[2];
            $data['admin_id'] = $operateId;
            $data['banquet_size'] = $row[3];
            $data['budget'] = $row[4];
            $data['is_valid'] = $row[5];
            $data['remark'] = $row[6];
            $data['wedding_date'] = $row[7];
            $data['zone'] = $row[8];
            $data['hotel_id'] = $row[9];
            $data['source_id'] = $row[10];
            $data['create_time'] = $time;
            $result = $MemberModel->insert($data);
            if ($result) {
                $memberId = $MemberModel->getLastInsID();
                $member[] = $memberId;
                ### 写入到手机号列表
                Member::pushMoblie($row[1], $memberId);
            }
        }

        $startArr = [];
        foreach ($manager as $k => $v) {
            $auth = UserAuth::getUserLogicAuth($k);
            $start = array_sum($startArr);
            $end = $start + $v;
            for ($start; $start < $end; $start++) {
                if (!isset($member[$start])) continue;
                $AllocateModel = new MemberAllocate();
                $data = [];
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $k;
                $data['member_id'] = $member[$start];
                $data['create_time'] = $time;
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
     * 添加客资视图
     * @return mixed
     */
    public function addCustomer()
    {
        ### 客资来源
        $sources = \app\index\model\Source::getSources();
        $this->assign('sources', $sources);

        ### 酒店列表
        $hotels = Hotel::getHotels();
        $this->assign("hotels", $hotels);

        $provinces = Region::getProvinceList();
        $this->assign('provinces', $provinces);

        $action = 'add';
        $this->assign('action', $action);

        return $this->fetch('edit_customer');
    }

    /**
     * 编辑客资视图
     * @return mixed
     */
    public function editCustomer()
    {
        $get = Request::param();
        ### 来源列表
        $sources = \app\index\model\Source::getSources();
        $this->assign('sources', $sources);

        ### 酒店列表
        $hotels = Hotel::getHotels();
        $this->assign("hotels", $hotels);

        $data = \app\index\model\Member::get($get['member_id']);
        $this->assign('data', $data);

        $provinces = Region::getProvinceList();
        $this->assign('provinces', $provinces);

        $action = 'edit';
        $this->assign('action', $action);
        return $this->fetch();
    }

    /**
     * 添加、编辑客资信息
     * @return \think\response\Json
     */
    public function doEditCustomer()
    {
        $post = Request::post();

        if ($post['id']) {
            $action = '编辑客资';
            $Model = \app\index\model\Member::get($post['id']);
        } else {

            if (empty($post['mobile'])) {
                return json([
                    'code' => '400',
                    'msg' => '手机号不能为空',
                ]);
            }

            if (empty($post['realname'])) {
                return json([
                    'code' => '400',
                    'msg' => '客户姓名不能为空',
                ]);
            }

            $action = '添加客资';
            $Model = new \app\index\model\Member();
            $Model->member_no = date('YmdHis') . rand(100, 999);
        }
        ### 开启事务
        $Model->startTrans();
        ### 验证手机号唯一性
        $memberedId = $Model::checkMobile($post['mobile']);
        if ($memberedId) {

            ### 加入重复日志
            $data = [];
            $data['user_id'] = $this->user['id'];
            $data['member_id'] = $memberedId;
            $data['source_id'] = $post['source_id'];
            $data['create_time'] = time();
            $DuplicateLog = new DuplicateLog();
            $DuplicateLog->insert($data);

            return json([
                'code' => '400',
                'msg' => $post['mobile'] . '手机号已经存在',
            ]);
        }
        ### 基本信息入库
        $result1 = $Model->save($post);

        ### 新添加客资要加入到分配列表中
        if (empty($post['id'])) {
            $data = [];
            // $data['operate_id'] = $this->user['id'];
            $data['user_id'] = $this->user['id'];
            $data['member_id'] = $Model->id;
            $data['source_id'] = $post['source_id'];
            $data['create_time'] = time();

            if (in_array($this->user['role_id'], [3,4])){ // 推荐组
                $Allocate = model('RecommendAllocate');
            } else if (in_array($this->user['role_id'], [10, 11])) { // 派单组
                $Allocate = model('DispatchAllocate');
            } else if (in_array($this->user['role_id'], [6,5,8])) { // 门市
                $Allocate = model('StoreAllocate');
            } else if ($this->user['role_id'] == 9){
                $Allocate = model('MerchantAllocate');
            } else {
                $Allocate = model('MemberAllocate');
            }

            $result2 = $Allocate->insert($data);
            /**
            $className = get_class($Allocate);
            if($className != 'MemberAllocate') {
                $data['is_sea'] = 1;
                $MemberAllocate = new MemberAllocate();
                $result3 = $MemberAllocate->insert($data);
            }**/
        } else {
            $result2 = 1;
        }

        if ($result1 && $result2) {
            ### 提交数据
            $Model->commit();
            ### 加入到手机号缓存
            $post['id'] && $Model::pushMoblie($post['mobile'], $Model->id);
            ### 同步公海数据
            if(isset($data['is_sea']) && $data['is_sea'] == 1) {
                $Model->save(['is_sea'=>1]);
            }

            ### 添加操作记录
            OperateLog::appendTo($Model);
            if(isset($Allocate)) OperateLog::appendTo($Allocate);
            return json(['code' => '200', 'msg' => $action . '成功']);
        } else {
            $Model->rollback();
            return json(['code' => '500', 'msg' => $action . '失败, 请重试']);
        }
    }

    /***
     * 分配到洗单组视图
     * @return mixed
     */
    public function assignToWashGroup()
    {
        $users = User::getUsers();
        foreach ($users as $key => $value) {
            if (!in_array($value['role_id'], [2,7])) {
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
    public function doAssignToWashGroup()
    {
        $post = Request::post();
        $ids = explode(',', $post['ids']);
        if(empty($ids)){
            return json([
                'code'  => '500',
                'msg'   => '请选择要分配的客户'
            ]);
        }

        $totals = count($ids);
        $success = 0;
        foreach ($ids as $id) {
            if (empty($id)) continue;
            $Model = MemberAllocate::get($id);
            $Model->user_id = $post['staff'];
            $Model->assign_status = 1;
            $result = $Model->save();
            if($result) {
                $success = $success + 1;
                OperateLog::appendTo($Model);
            }
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
        $users = User::getUsers();
        foreach ($users as $key => $value) {
            if (!in_array($value['role_id'], [3,4])) {
                unset($users[$key]);
            }
        }
        $this->assign('users', $users);

        return $this->fetch();
    }

    /**
     * 分配到推荐组主管逻辑
     * @return \think\response\Json
     */
    public function doAssignToRecommendGroup()
    {
        $post = Request::post();
        $ids = explode(',', $post['ids']);
        if(empty($ids)){
            return json([
                'code'  => '500',
                'msg'   => '请选择要分配的客户'
            ]);
        }

        $totals = count($ids);
        $success = 0;
        foreach ($ids as $id) {
            if (empty($id)) continue;

            if(in_array($this->user['role_id'], [1, 2, 7])) { // 洗单组
                $From = MemberAllocate::get($id);
                $From->active_assign_status = 1;
                $From->is_sea = 1;
            }  else { // 按照推荐组处理自己推给自己处理
                $From = RecommendAllocate::get($id);
                $From->assign_status = 1;
            }

            $memberId = $From->member_id;
            $Member = Member::get($memberId);
            $Member->save(['is_sea'=>1]);

            $map = [];
            $map[] = ['member_id', '=', $memberId];
            $map[] = ['user_id', '=', $post['staff']];
            $NewAllocate = new RecommendAllocate();
            $NewAllocate->startTrans();
            $allocate = $NewAllocate->where($map)->find();
            if(!empty($allocate)) continue;

            $data = $From->getData();
            unset($data['id']);
            $data['operate_id'] = $this->user['id'];
            $data['user_id'] = $post['staff'];
            $data['is_sea'] = 1;
            $data['create_time'] = time();
            $result1 = $NewAllocate->save($data);
            $result2 = $From->save();
            if($result1 && $result2) {
                $NewAllocate->commit();
                $success = $success + 1;
                OperateLog::appendTo($NewAllocate);
            } else {
                $NewAllocate->rollback();
            }
        }
        $fail = $totals - $success;
        return json([
            'code' => '200',
            'msg' => "分配到推荐组:成功{$success}个,失败{$fail}个"
        ]);
    }

    /**
     * 分配到门店客服视图
     * @return mixed
     */
    public function assignToStoreStaff()
    {

        $stores = Store::getStoresWithUser();
        $this->assign('stores', $stores);

        return $this->fetch();
    }

    /**
     * @return \think\response\Json
     */
    public function doAssignToStoreStaff()
    {
        $post = Request::post();
        $ids = explode(',', $post['ids']);
        if(empty($ids)){
            return json([
                'code'  => '500',
                'msg'   => '请选择要分配的客户'
            ]);
        }

        $totals = count($ids);
        $success = 0;
        foreach ($ids as $id) {
            if (empty($id)) continue;

            if(in_array($this->user['role_id'], [1, 15])) { // 管理员、客服经理
                $From = MemberAllocate::get($id);
                $From->active_assign_status = 1;
            } else if (in_array($this->user['role_id'], [3,4])) { // 推荐组
                $From = RecommendAllocate::get($id);
                $From->assign_status = 1;
            } else if (in_array($this->user['role_id'], [10,11])) { // 推荐组
                $From = DispatchAllocate::get($id);
                $From->assign_status = 1;
            } else {
                return json([
                    'code' => '500',
                    'msg' => '当前角色,没有权限分配客资到门店'
                ]);
            }

            $memberId = $From->member_id;
            $map = [];
            $map[] = ['member_id', '=', $memberId];
            $map[] = ['user_id', '=', $post['staff']];
            $NewAllocate = new StoreAllocate();
            $NewAllocate->startTrans();
            $allocate = $NewAllocate->where($map)->find();
            if(!empty($allocate)) continue;

            $data = $From->getData();
            unset($data['id']);
            $data['operate_id'] = $this->user['id'];
            $data['user_id'] = $post['staff'];
            $data['is_sea'] = 1;
            $data['create_time'] = time();
            $result1 = $NewAllocate->save($data);
            $result2 = $From->save();
            if($result1 && $result2) {
                $NewAllocate->commit();
                $success = $success + 1;
                OperateLog::appendTo($NewAllocate);
            } else {
                $NewAllocate->rollback();
            }
        }
        $fail = $totals - $success;
        return json([
            'code' => '200',
            'msg' => "分配到推荐组:成功{$success}个,失败{$fail}个"
        ]);
    }

    /**
     * 删除客资
     * @return \think\response\Json
     */
    public function deleteCustomer()
    {
        $get = Request::param();
        $result = \app\index\model\Member::get($get['id'])->delete();

        if ($result) {
            // 更新缓存
            \app\index\model\Member::updateCache();
            return json(['code' => '200', 'msg' => '删除成功']);
        } else {
            return json(['code' => '500', 'msg' => '删除失败']);
        }
    }

    /**
     * 我的申请
     * @return mixed|\think\response\Json
     */
    public function apply()
    {
        $get = Request::param();
        if (Request::isAjax()) {
            $config = [
                'page' => $get['page']
            ];
            $map[] = ['user_id', '=', $this->user['id']];
            if (isset($get['status'])) {
                $map[] = ['apply_status', '=', $get['status']];
            }
            $list = model('MemberApply')->where($map)->with('member')->paginate($get['limit'], false, $config);
            $data = $list->getCollection()->toArray();
            foreach ($data as &$value) {
                $member = $value['member'];
                unset($member['id']);
                unset($value['member']);
                $value = array_merge($value, $member);
                $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                $value['news_type'] = $this->newsTypes[$value['news_type']];
                $value['hotel_id'] = $this->hotels[$value['hotel_id']]['title'];
                if($this->auth['is_show_alias'] == '1') {
                    $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['alias'] : '未知';
                } else {
                    $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : '未知';
                }
            }

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $data
            ];
            return json($result);

        } else {

            $tabs = Tab::customerApply($get);
            $this->assign('tabs', $tabs);
            return $this->fetch();
        }
    }

    /**
     * 申请客资回访
     * @return \think\response\Json
     */
    public function doApply()
    {
        $ids = Request::post("ids");
        $ids = explode(',', $ids);
        if (empty($ids)) {
            return json([
                'code' => '500',
                'msg' => '申请的客资不能为空'
            ]);
        }

        $count = count($ids);
        $success = 0;
        $user = session('user');
        foreach ($ids as $id) {
            $allocate = Allocate::getAllocate($this->user, $id);
            if (!empty($allocate)) continue;

            $Model = new MemberApply();
            $data['user_id'] = $user['id'];
            $data['member_id'] = $id;
            $data['apply_status'] = 0;
            $data['create_time'] = time();
            $res = $Model->insert($data);
            if ($res) $success = $success + 1;
        }

        $fail = $count - $success;
        return json([
            'code' => '200',
            'msg' => "申请成功{$success}条,失败{$fail}条"
        ]);
    }

    /**
     * 补充get参数
     * @return mixed
     */
    public function visitCustomer()
    {
        $get = Request::param();
        $allocate = Allocate::getAllocate($this->user, $get['member_id'])->getData();

        ### 获取用户基本信息
        $auth = UserAuth::getUserLogicAuth($this->user['id']);
        $customer = Member::get($get['member_id']);
        if (!($auth['is_show_entire_mobile'] || $allocate['user_id'] == $this->user['id'])) {
            $customer['mobile'] = substr_replace($customer['mobile'], '****', 3, 4);
        }
        $this->assign('customer', $customer);
        ### 获取回访日志,检测是否拥有查看所有回访的权限
        $visits = MemberVisit::getMemberVisitList($auth, $get['member_id']);
        $this->assign('visits', $visits);

        $provinces = Region::getProvinceList();
        $this->assign('provinces', $provinces);

        $cities = [];
        if($customer['province_id']) {
            $cities = Region::getCityList($customer['province_id']);
        }
        $this->assign('cities', $cities);

        $areas = [];
        if($customer['city_id']) {
            $areas = Region::getAreaList($customer['city_id']);
        }
        $this->assign('areas', $areas);

        // 获取意向状态、来源、酒店、权限
        $this->assign('intentions', $this->status);
        $this->assign('sources', $this->sources);
        $this->assign('hotels', $this->hotels);
        $this->assign("auth", $auth);
        return $this->fetch();
    }

    /**
     * 回访客资逻辑
     * $post['member_id']
     * @return \think\response\Json
     */
    public function doVisitCustomer()
    {
        $post = Request::post();
        $Model = model("MemberVisit");
        $Model->startTrans();
        #### 检测用户是否拥有回访权限
        $allocate = Allocate::getAllocate($this->user, $post['member_id']);
        $allocateData = $allocate->getData();
        if(empty($allocateData)) {
            $Model->rollback();
            return json(['code' => '500', 'msg' => '回访失败']);
        }
        ### 保存回访信息
        $post['next_visit_time'] = strtotime($post['next_visit_time']);
        $Model->user_id = $this->user['id'];
        $result1 = $Model->save($post);

        ### 该客资的回放次数+1
        $Member = Member::get($allocateData['member_id']);
        if (isset($post['status'])) {
            $Member->status = $post['status'];
        }
        $Member->visit_amount = ['inc', 1];
        $result2 = $Member->save($post);

        ### 同步到分配信息
        $result3 = Allocate::updateAllocateByModel($allocate, $this->user, $post);


        ### result2和3不是每次都修改成功的
        if ($result1) {
            $Model->commit();
            ### 记录log日志
            OperateLog::appendTo($Model);
            return json(['code' => '200', 'msg' => '回访成功']);
        } else {
            $Model->rollback();
            return json(['code' => '500', 'msg' => '回访失败']);
        }
    }

    /**
     * 回访客资逻辑
     * @return mixed
     */
    public function visitLogs()
    {
        $get = Request::param();
        ### 获取用户基本信息
        $customer = Member::get($get['member_id']);
        $customer['mobile'] = substr_replace($customer['mobile'], '****', 3, 4);
        $this->assign('customer', $customer);
        ### 获取回访日志,检测是否拥有查看所有回访的权限
        $auth = UserAuth::getUserLogicAuth($this->user['id']);
        $visits = MemberVisit::getMemberVisitList($auth, $get['member_id']);
        $this->assign('visits', $visits);

        // 获取意向状态、来源、酒店、权限
        $this->assign('intentions', $this->status);
        $this->assign('sources', $this->sources);
        $this->assign('hotels', $this->hotels);
        $this->assign("auth", $auth);

        return $this->fetch();
    }
}