<?php
namespace app\index\controller;

use app\index\model\Csv;
use app\index\model\Hotel;
use app\index\model\Intention;
use app\index\model\Member;
use app\index\model\MemberAllocate;
use app\index\model\MemberApply;
use app\index\model\Source;
use app\index\model\Store;
use app\index\model\User;
use app\index\model\UserAuth;
use think\facade\Request;
use think\facade\Session;

class Customer extends Base
{


    public function index()
    {
        $user = Session::get("user");
        $auth = UserAuth::getUserLogicAuth($user['id']);
        $roleIds = $auth['role_ids'];
        $get = Request::param();
        if (Request::isAjax()) {
            $sources = \app\index\model\Source::getSources();
            $intentions = Intention::getIntentions();
            $hotels = Hotel::getHotels();
            $newsTypes = ['婚宴信息', '婚庆信息', '一站式'];
            $config = [
                'page' => $get['page']
            ];

            switch ($roleIds) {
                case 7: // 洗单组主管
                    $map[] = ['wash_manager_id', '=', $user['id']];
                    // 分配到洗单客服状态过滤
                    if (isset($get['assign_status'])) {
                        $map[] = ['wash_manager_assign_status', '=', $get['assign_status']];
                    }

                    // 有效客资分配到推荐组主管状态
                    if (isset($get['active_assign_status'])) {
                        $map[] = ['wash_manager_active_assign_status', '=', $get['active_assign_status']];
                    }

                    // 洗单客服回访状态过滤
                    if (isset($get['status'])) {
                        $map[] = ['wash_status', '=', $get['status']];
                    }
                    break;
                case 2: // 洗单组客服
                    $map[] = ['wash_staff_id', '=', $user['id']];

                    // 洗单客服回访状态过滤
                    if (isset($get['status'])) {
                        $map[] = ['wash_status', '=', $get['status']];
                    }
                    break;
                case 3: // 推荐组主管
                    $map[] = ['recommend_manager_id', '=', $user['id']];
                    // 分配到推荐组客服状态过滤
                    if (isset($get['assign_status'])) {
                        $map[] = ['recommend_manager_assign_status', '=', $get['assign_status']];
                    }
                    break;
                case 4: // 推荐组客服
                    $map[] = ['recommend_staff_id', '=', $user['id']];
                    if (isset($get['assign_status'])) {
                        $map[] = ['recommend_staff_assign_status', '=', $get['assign_status']];
                    }
                    break;
                case 10: // 派单组主管
                    $map[] = ['dispatch_manager_id', '=', $user['id']];
                    if (isset($get['assign_status'])) {
                        $map[] = ['dispatch_manager_assign_status', '=', $get['assign_status']];
                    }
                    break;
                case 11: // 派单组客服
                    $map[] = ['dispatch_staff_id', '=', $user['id']];
                    if (isset($get['assign_status'])) {
                        $map[] = ['dispatch_staff_assign_status', '=', $get['assign_status']];
                    }
                    break;
                default :
                    $map[] = ['user_id', '=', $user['id']];
            }
            $list = model('MemberAllocate')->where($map)->with('member')->paginate($get['limit'], false, $config);

            if (!empty($list)) {
                $data = $list->getCollection()->toArray();
                foreach ($data as &$value) {
                    $member = $value['member'];
                    unset($member['id']);
                    unset($value['member']);
                    $value = array_merge($value, $member);
                    $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                    $value['news_type'] = $newsTypes[$value['news_type']];
                    $value['hotel_id'] = $hotels[$value['hotel_id']]['title'];
                    $value['intention_status'] = $value['intention_status'] ? $intentions[$value['intention_status']] : "跟进中";
                    $value['source_id'] = $sources[$value['source_id']] ? $sources[$value['source_id']]['title'] : '未知';
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
            switch ($roleIds) {
                case 7: // 洗单组主管
                    $tabs = [
                        ['text' => '已分配', 'url' => url('customer/index', ['assign_status' => 1]), 'checked' => $get['assign_status'] === '1' ? 1 : 0],
                        ['text' => '未分配', 'url' => url('customer/index', ['assign_status' => 0]), 'checked' => $get['assign_status'] === '0' ? 1 : 0],
                        ['text' => '有效已分配', 'url' => url('customer/index', ['active_assign_status' => 1]), 'checked' => $get['active_assign_status'] === '1' ? 1 : 0],
                        ['text' => '有效未分配', 'url' => url('customer/index', ['active_assign_status' => 0]), 'checked' => $get['active_assign_status'] === '0' ? 1 : 0],
                        ['text' => '无效客资', 'url' => url('customer/index', ['status' => 2]), 'checked' => $get['status'] === '2' ? 1 : 0],
                        ['text' => '未跟进', 'url' => url('customer/index', ['status' => 0]), 'checked' => $get['status'] === '0' ? 1 : 0],
                    ];
                    break;
                case 2: // 洗单组客服
                    $tabs = [
                        ['text' => '有效客资', 'url' => url('customer/index', ['status' => 1]), 'checked' => $get['status'] === '1' ? 1 : 0],
                        ['text' => '无效客资', 'url' => url('customer/index', ['status' => 2]), 'checked' => $get['status'] === '2' ? 1 : 0],
                        ['text' => '未跟进', 'url' => url('customer/index', ['status' => 0]), 'checked' => $get['status'] === '1' ? 1 : 0],
                    ];
                    break;
                case 3: // 推荐组主管
                    $tabs = [
                        ['text' => '已分配', 'url' => url('customer/index', ['assign_status' => 1]), 'checked' => $get['assign_status'] === '1' ? 1 : 0],
                        ['text' => '未分配', 'url' => url('customer/index', ['assign_status' => 0]), 'checked' => $get['assign_status'] === '0' ? 1 : 0],
                    ];
                    break;
                case 4: // 推荐组客服
                    $tabs = [
                        ['text' => '已分配', 'url' => url('customer/index', ['assign_status' => 1]), 'checked' => $get['assign_status'] === '1' ? 1 : 0],
                        ['text' => '未分配', 'url' => url('customer/index', ['assign_status' => 0]), 'checked' => $get['assign_status'] === '0' ? 1 : 0],
                    ];
                    break;
                case 10: // 派单组主管
                    $tabs = [
                        ['text' => '已分配', 'url' => url('customer/index', ['assign_status' => 1]), 'checked' => $get['assign_status'] === '1' ? 1 : 0],
                        ['text' => '未分配', 'url' => url('customer/index', ['assign_status' => 0]), 'checked' => $get['assign_status'] === '0' ? 1 : 0],
                    ];
                    break;
                case 11: // 派单组客服
                    $tabs = [
                        ['text' => '已分配', 'url' => url('customer/index', ['assign_status' => 1]), 'checked' => $get['assign_status'] === '1' ? 1 : 0],
                        ['text' => '未分配', 'url' => url('customer/index', ['assign_status' => 0]), 'checked' => $get['assign_status'] === '0' ? 1 : 0],
                    ];
                    break;
                default :
                    $tabs = [
                        ['text' => '所有客资', 'url' => url('customer/index'), 'checked' => 1],
                    ];
            }
            $this->assign('tabs', $tabs);
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
        $user = Session::get("user");
        $auth = UserAuth::getUserLogicAuth($user['id']);
        $roleIds = $auth['role_ids'];

        if (Request::isAjax()) {
            $sources = \app\index\model\Source::getSources();
            $intentions = Intention::getIntentions();
            $hotels = Hotel::getHotels();
            $newsTypes = ['婚宴信息', '婚庆信息', '一站式'];

            $get = Request::param();
            $config = [
                'page' => $get['page']
            ];

            switch ($roleIds) {
                case 7: // 洗单组主管
                    $map[] = ['wash_manager_id', '=', $user['id']];
                    // 查询回访状态
                    if (isset($get['status'])) {
                        $map[] = ['wash_status', '=', $get['status']];
                    }
                    break;
                case 2: // 洗单组客服
                    $map[] = ['wash_staff_id', '=', $user['id']];
                    if (isset($get['status'])) {
                        $map[] = ['wash_status', '=', $get['status']];
                    }
                    break;
                case 3: // 推荐组主管
                    $map[] = ['recommend_manager_id', '=', $user['id']];
                    if (isset($get['status'])) {
                        $map[] = ['intention_status', '=', $get['status']];
                    }
                    break;
                case 4: // 推荐组客服
                    $map[] = ['recommend_staff_id', '=', $user['id']];
                    if (isset($get['status'])) {
                        $map[] = ['intention_status', '=', $get['status']];
                    }
                    break;
                case 10: // 派单组主管
                    $map[] = ['dispatch_manager_id', '=', $user['id']];
                    if (isset($get['status'])) {
                        $map[] = ['intention_status', '=', $get['status']];
                    }
                    break;
                case 11: // 派单组客服
                    $map[] = ['dispatch_staff_id', '=', $user['id']];
                    if (isset($get['status'])) {
                        $map[] = ['intention_status', '=', $get['status']];
                    }
                    break;
                default :
                    $map[] = ['user_id', '=', $user['id']];
                    if (isset($get['status'])) {
                        $map[] = ['intention_status', '=', $get['status']];
                    }
            }

            if (isset($get['order_status'])) {
                $map[] = ['order_status', '=', $get['order_status']];
            }

            $get['keywords'] = trim($get['keywords']);
            if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
                $map = [];
                $map[] = ['mobile', '=', $get['keywords']];
                $list = MemberAllocate::hasWhere('member', $map)->with('member')->paginate($get['limit'], false, $config);
            } else if (isset($get['keywords']) && strlen($get['keywords'] < 11)) {

            } else if (isset($get['keywords']) && strlen($get['keywords']) > 11) {

            } else {
                $list = model('MemberAllocate')->where($map)->with('member')->paginate($get['limit'], false, $config);
            }


            if(!empty($list)) {
                $data = $list->getCollection()->toArray();
                foreach ($data as &$value) {
                    $member = $value['member'];
                    unset($member['id']);
                    unset($value['member']);
                    $value = array_merge($value, $member);
                    $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                    $value['news_type'] = $newsTypes[$value['news_type']];
                    $value['hotel_id'] = $hotels[$value['hotel_id']]['title'];
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
            // 生成导航
            switch ($roleIds) {
                case 7:
                case 2:
                    $tabs = [
                        ['text' => '所有客资', 'url' => url('customer/mine'), 'checked' => !isset($get['status']) && !isset($get['order_status']) ? 1 : 0],
                        ['text' => '待跟进', 'url' => url('customer/mine', ['status' => 0]), 'checked' => isset($get['status']) && $get['status'] === '0' ? 1 : 0],
                        ['text' => '有效客资', 'url' => url('customer/mine', ['status' => 1]), 'checked' => isset($get['status']) && $get['status'] === '1' ? 1 : 0],
                        ['text' => '无效客资', 'url' => url('customer/mine', ['status' => 3]), 'checked' => isset($get['status']) && $get['status'] === '3' ? 1 : 0],
                        ['text' => '成单客资', 'url' => url('customer/mine', ['order_status' => 1]), 'checked' => isset($get['order_status']) && $get['order_status'] === '1' ? 1 : 0],
                    ];
                    break;
                default :
                    $tabs = [
                        ['text' => '所有客资', 'url' => url('customer/mine'), 'checked' => !isset($get['status']) && !isset($get['order_status']) ? 1 : 0],
                        ['text' => '待跟进', 'url' => url('customer/mine', ['status' => 0]), 'checked' => isset($get['status']) && $get['status'] === '0' ? 1 : 0],
                        ['text' => '跟进中', 'url' => url('customer/mine', ['status' => 2]), 'checked' => isset($get['status']) && $get['status'] === '2' ? 1 : 0],
                        ['text' => '有效客资', 'url' => url('customer/mine', ['status' => 1]), 'checked' => isset($get['status']) && $get['status'] === '1' ? 1 : 0],
                        ['text' => '无效客资', 'url' => url('customer/mine', ['status' => 3]), 'checked' => isset($get['status']) && $get['status'] === '3' ? 1 : 0],
                        ['text' => '失效客资', 'url' => url('customer/mine', ['status' => 4]), 'checked' => isset($get['status']) && $get['status'] === '4' ? 1 : 0],
                        ['text' => '成单客资', 'url' => url('customer/mine', ['order_status' => 1]), 'checked' => isset($get['order_status']) && $get['order_status'] === '1' ? 1 : 0],
                    ];
            }
            $this->assign('tabs', $tabs);
            return $this->fetch();
        }
    }

    /**
     * 我的申请
     * @return mixed|\think\response\Json
     */
    public function apply()
    {
        $user = session("user");
        $get = Request::param();
        if (Request::isAjax()) {
            $sources = \app\index\model\Source::getSources();
            $intentions = Intention::getIntentions();
            $hotels = Hotel::getHotels();
            $newsTypes = ['婚宴信息', '婚庆信息', '婚宴转婚庆'];

            $user = Session::get("user");
            $config = [
                'page' => $get['page']
            ];
            $map[] = ['user_id', '=', $user['id']];
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
                $value['news_type'] = $newsTypes[$value['news_type']];
                $value['hotel_id'] = $hotels[$value['hotel_id']]['title'];
                $value['source_id'] = isset($sources[$value['source_id']]) && $sources[$value['source_id']] ? $sources[$value['source_id']]['title'] : '未知';
            }
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $data
            ];
            return json($result);

        } else {
            $tabs = [
                ['text' => '所有申请', 'url' => url('customer/apply'), 'checked' => !isset($get['status']) && !isset($get['order_status']) ? 1 : 0],
                ['text' => '未审核', 'url' => url('customer/apply', ['status' => 0]), 'checked' => isset($get['status']) && $get['status'] === '0' ? 1 : 0],
                ['text' => '已通过', 'url' => url('customer/apply', ['status' => 1]), 'checked' => isset($get['status']) && $get['status'] === '1' ? 1 : 0],
                ['text' => '未通过', 'url' => url('customer/apply', ['status' => 2]), 'checked' => isset($get['status']) && $get['status'] === '2' ? 1 : 0],
            ];
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
            $sources = \app\index\model\Source::getSources();
            $intentions = Intention::getIntentions();
            $hotels = Hotel::getHotels();
            $newsTypes = ['婚宴信息', '婚庆信息', '婚宴转婚庆'];
            $config = [
                'page' => $get['page']
            ];

            $map[] = ['is_sea', '=', '1'];
            $list = model('MemberAllocate')->where($map)->with('member')->paginate($get['limit'], false, $config);
            $data = $list->getCollection()->toArray();
            foreach ($data as &$value) {
                $member = $value['member'];
                unset($member['id']);
                unset($value['member']);
                $value = array_merge($value, $member);
                $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                $value['news_type'] = $newsTypes[$value['news_type']];
                $value['hotel_id'] = $hotels[$value['hotel_id']]['title'];
                $value['intention_status'] = $value['intention_status'] ? $intentions[$value['intention_status']] : "跟进中";
                $value['source_id'] = $sources[$value['source_id']] ? $sources[$value['source_id']]['title'] : '未知';
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
                // $this->assign('file', $cacheData);
                return json(['code' => '200', 'msg' => '上传成功,请继续分配', 'data' => $cacheData]);
            } else {
                return json(['code' => '500', 'msg' => '上传失败']);
            }
        }
        $upload = redis()->hMget($hashKey, ['file', 'amount', 'repeat']);
        $this->assign('upload', $upload);

        return $this->fetch();
    }

    /**
     * 导出重复客资
     */
    public function export()
    {

    }

    /***
     * 上传并分配到洗单组主管视图
     * @return mixed
     */
    public function allocate()
    {
        $users = User::getUsers();
        foreach ($users as $key => $value) {
            $auth = UserAuth::getUserLogicAuth($value["id"]);
            $roleIds = explode(',', $auth['role_ids']);
            if (!in_array(7, $roleIds) && !in_array(2, $roleIds)) {
                unset($users[$key]);
            }
        }
        $this->assign('users', $users);

        $user = Session::get("user");
        $hashKey = "batch_upload:" . $user['id'];
        $fileData = redis()->hMGet($hashKey, ['file', 'amount']);
        $this->assign('fileData', $fileData);


        return $this->fetch();
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
            // return json(['code'=>'500', 'msg'=>'分配数量小于上传数量']);
        }
        if ($sum > $fileData['amount']) {
            // return json(['code'=>'500', 'msg'=>'分配数量大于上传数量']);
        }

        ### 开始分配
        $time = time();
        $result = Csv::readCsv($fileData['file']);
        $member = [];
        foreach ($result[0] as $key => $row) {
            if ($row[0] == '客户名称') continue;
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
            $data['create_time'] = $time;
            $result = $MemberModel->insert($data);
            if ($result) {
                $member[] = $MemberModel->getLastInsID();
            }
        }

        $startArr = [];
        foreach ($manager as $k => $v) {
            $start = array_sum($startArr);
            $end = $start + $v;
            for ($start; $start < $end; $start++) {
                if (!isset($member[$start])) continue;
                $AllocateModel = new MemberAllocate();
                $data = [];
                $data['operate_id'] = $operateId;
                $data['manager_id'] = $k;
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

        $data = \app\index\model\Member::get($get['id']);
        $this->assign('data', $data);

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
        if (empty($post['mobile'])) {
            return json([
                'code' => '400',
                'msg' => '手机号不能为空',
            ]);
        }

        if (empty($post['realname'])) {
            return json([
                'code' => '400',
                'msg' => '客户称谓不能为空',
            ]);
        }

        if ($post['id']) {
            $action = '编辑客资';
            $Model = \app\index\model\Member::get($post['id']);
        } else {
            $action = '添加客资';
            $Model = new \app\index\model\Member();
            $Model->member_no = date('YmdHis') . rand(100, 999);
        }
        ### 开启事务
        $Model->startTrans();

        ### 验证手机号唯一性
        $checked1 = $Model::checkMobile($post['mobile']);
        if ($checked1) {
            return json([
                'code' => '400',
                'msg' => $post['mobile'] . '手机号已经存在',
            ]);
        }

        ### 基本信息入库
        $result1 = $Model->save($post);
        $user = session("user");

        ### 新添加客资要加入到分配列表中
        if (empty($post['id'])) {
            $data = [];
            // $data
            $data['operate_id'] = $user['id'];
            $data['user_id'] = $user['id'];
            $data['member_id'] = $Model->id;
            $data['source_id'] = $post['source_id'];
            $data['create_time'] = time();
            ### 根据用户类型来补齐业务流转数据
            $auth = UserAuth::getUserLogicAuth($user['id']);
            switch ($auth['role_ids']) {
                case 7: // 洗单组主管

                    break;
                case 2: // 洗单组客服

                    break;
                case 3: // 推荐组主管

                    break;
                case 4: // 推荐组客服

                    break;
                case 10: // 派单组主管

                    break;

                case 11: // 派单组客服

                    break;
            }

            $MemberAllocate = new MemberAllocate();
            $result2 = $MemberAllocate->insert($data);
        }

        if ($result1 && $result2) {
            ### 提交数据
            $Model->commit();

            ### 加入到手机号缓存
            $Model::pushMoblie($post['mobile']);
            return json(['code' => '200', 'msg' => $action . '成功']);
        } else {
            $Model->rollback();
            return json(['code' => '500', 'msg' => $action . '失败, 请重试']);
        }
    }

    /***
     * 分配到洗单组主管视图
     * @return mixed
     */
    public function assignToWashManager()
    {
        $users = User::getUsers();
        foreach ($users as $key => $value) {
            $auth = UserAuth::getUserLogicAuth($value["id"]);
            $roleIds = explode(',', $auth['role_ids']);
            if (!in_array(7, $roleIds)) {
                unset($users[$key]);
            }
        }
        $this->assign('users', $users);
        return $this->fetch();
    }

    /**
     * 分配到洗单组客服逻辑
     * @return \think\response\Json
     */
    public function doAssignToWashManager()
    {
        $post = Request::post();
        $ids = explode(',', $post['ids']);

        foreach ($ids as $id) {
            $Model = new MemberAllocate();
            $map = [];
            $map[] = ['id', '=', $id];

            $data['wash_manager_id'] = $post['manager'];
            $data['wash_manager_assign_status'] = 1;
            $Model->save([$data, $map]);
        }

        return json([
            'code' => '200',
            'msg' => '分配到洗单组客服成功'
        ]);
    }

    /**
     * 分配到洗单组客服视图
     * @return mixed
     */
    public function assignToWashStaff()
    {
        $users = User::getUsers();
        foreach ($users as $key => $value) {
            $auth = UserAuth::getUserLogicAuth($value["id"]);
            $roleIds = explode(',', $auth['role_ids']);
            if (!in_array(2, $roleIds)) {
                unset($users[$key]);
            }
        }
        $this->assign('users', $users);
        return $this->fetch();
    }

    /**
     * 分配到洗单组客服逻辑
     * @return \think\response\Json
     */
    public function doAssignToWashStaff()
    {
        $post = Request::post();
        $ids = explode(',', $post['ids']);

        foreach ($ids as $id) {
            $Model = new MemberAllocate();
            $map = [];
            $map[] = ['id', '=', $id];

            $data['customer_staff_id'] = $post['staff'];
            $Model->save($data, $map);
        }

        return json([
            'code' => '200',
            'msg' => '分发到二级客服成功'
        ]);
    }

    /**
     * 分配到推荐组主管视图
     * @return mixed
     */
    public function assignToRecommendManager()
    {
        $users = User::getUsers();
        foreach ($users as $key => $value) {
            $auth = UserAuth::getUserLogicAuth($value["id"]);
            $roleIds = explode(',', $auth['role_ids']);
            if (!in_array(3, $roleIds)) {
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
    public function doAssignToRecommendManager()
    {
        $post = Request::post();
        $ids = explode(',', $post['ids']);

        foreach ($ids as $id) {
            $Model = new MemberAllocate();
            $map = [];
            $map[] = ['id', '=', $id];
            $data['recommend_manager_id'] = $post['staff'];
            $data['recommend_manager_assign_status'] = 1;
            // 标记公海客资
            $data['is_sea'] = 1;
            $Model->save($data, $map);
        }

        return json([
            'code' => '200',
            'msg' => '分发到二级客服成功'
        ]);
    }

    /**
     * 分配到推荐组客服视图
     * @return mixed
     */
    public function assignToRecommendStaff()
    {
        $users = User::getUsers();
        foreach ($users as $key => $value) {
            $auth = UserAuth::getUserLogicAuth($value["id"]);
            $roleIds = explode(',', $auth['role_ids']);
            if (!in_array(4, $roleIds)) {
                unset($users[$key]);
            }
        }
        $this->assign('users', $users);

        return $this->fetch();
    }

    /**
     * 分配到推荐组客服逻辑
     * @return \think\response\Json
     */
    public function doAssignToRecommendStaff()
    {
        $post = Request::post();
        $ids = explode(',', $post['ids']);

        foreach ($ids as $id) {
            $Model = new MemberAllocate();
            $map = [];
            $map[] = ['id', '=', $id];

            $data['recommend_staff_id'] = $post['staff'];
            $Model->save($data, $map);
        }

        return json([
            'code' => '200',
            'msg' => '分发到二级客服成功'
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

        foreach ($ids as $id) {
            $Model = new MemberAllocate();
            $map = [];
            $map[] = ['id', '=', $id];
            $Model->save(['store_id' => $post['store_id']], $map);
        }

        return json([
            'code' => '200',
            'msg' => '分发到门店成功'
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
            $allocate = MemberAllocate::get($id)->toArray();
            $allocate['member_allocate_id'] = $allocate['id'];
            $Model = new MemberApply();
            $data['user_id'] = $user['id'];
            $data['recommend_manager_id'] = $allocate['recommend_manager_id'];
            $data['recommend_staff_id'] = $allocate['recommend_staff_id'];
            $data['dispatch_manager_id'] = $allocate['dispatch_manager_id'];
            $data['dispatch_staff_id'] = $allocate['dispatch_staff_id'];
            $data['member_allocate_id'] = $allocate['id'];
            $data['member_id'] = $allocate['member_id'];
            $data['brand_id'] = $allocate['brand_id'];
            $data['store_id'] = $allocate['store_id'];
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
     * 回访客资
     * @return mixed
     */
    public function visitCustomer()
    {
        // 获取用户信息
        $user = Session::get('user');
        $auth = UserAuth::getUserLogicAuth($user['id']);
        $this->assign('auth', $auth);
        $roleIds = $auth['role_ids'];
        // 获取意向状态
        if (in_array($roleIds, [2, 7])) { // 洗单组回访
            $intentions = ['跟进中', '有效客资', '无效客资'];
        } else {
            $intentions = Intention::getIntentions();
        }
        $this->assign('intentions', $intentions);

        // 获取来源
        $sources = Source::getSources();
        // 酒店列表
        $hotels = Hotel::getHotels();
        $get = Request::param();
        ### 获取用户基本信息
        $newsTypes = ['婚宴信息', '婚庆信息', '一站式'];
        $customer = Member::get($get['id']);
        if (!$auth['is_show_entire_mobile']) {
            $customer['mobile'] = substr_replace($customer['mobile'], '****', 3, 4);
        }
        $customer['news_type'] = $newsTypes[$customer['news_type']];
        $customer['source_id'] = $sources[$customer['source_id']]['title'];
        $customer['hotel_id'] = $hotels[$customer['hotel_id']]['title'];
        $this->assign('customer', $customer);

        ### 获取回访日志,检测是否拥有查看所有回访的权限
        if (!$auth['show_visit_log']) {
            // 只能查看自己的回访记录
            $map[] = ['user_id', '=', $user['id']];
        }
        $map[] = ['member_id', '=', $get['id']];
        $visits = model('MemberVisit')->where($map)->select()->toArray();
        foreach ($visits as &$value) {
            $time = strtotime($value['create_time']);
            $value['create_time'] = date('y/m/d H:i', $time);
            $value['status'] = $intentions[$value['status']]['title'];
        }
        $this->assign('visits', $visits);

        return $this->fetch();
    }

    /**
     * 回访客资逻辑
     * @return \think\response\Json
     */
    public function doVisit()
    {
        $post = Request::post();
        $post['next_visit_time'] = strtotime($post['next_visit_time']);
        if ($post['id']) {
            $action = '编辑回访成功';
            $Model = \app\index\model\MemberVisit::get($post['id']);
        } else {
            $action = '添加回访成功';
            $Model = new \app\index\model\MemberVisit();
        }

        $user = Session::get("user");
        $Model->user_id = $user['id'];

        // $Model::create($post);
        $result = $Model->save($post);
        if ($result) {
            // empty($post['id']) && $post['id'] = $Model->id;
            return json(['code' => '200', 'msg' => $action . '成功']);
        } else {
            return json(['code' => '500', 'msg' => $action . '失败']);
        }
    }

    /**
     * 回访客资逻辑
     * @return mixed
     */
    public function visitLogs()
    {
        // 获取用户信息
        $user = Session::get('user');
        $auth = UserAuth::getUserLogicAuth($user['id']);
        $this->assign('auth', $auth);
        $roleIds = $auth['role_ids'];
        // 获取意向状态
        if (in_array($roleIds, [2, 7])) { // 洗单组回访
            $intentions = ['跟进中', '有效客资', '无效客资'];
        } else {
            $intentions = Intention::getIntentions();
        }
        $this->assign('intentions', $intentions);

        // 获取来源
        $sources = Source::getSources();
        // 酒店列表
        $hotels = Hotel::getHotels();
        $get = Request::param();
        ### 获取用户基本信息
        $newsTypes = ['婚宴信息', '婚庆信息', '一站式'];
        $customer = Member::get($get['id']);
        if (!$auth['is_show_entire_mobile']) {
            $customer['mobile'] = substr_replace($customer['mobile'], '****', 3, 4);
        }
        $customer['news_type'] = $newsTypes[$customer['news_type']];
        $customer['source_id'] = $sources[$customer['source_id']]['title'];
        $customer['hotel_id'] = $hotels[$customer['hotel_id']]['title'];
        $this->assign('customer', $customer);

        ### 获取回访日志,检测是否拥有查看所有回访的权限
        if (!$auth['show_visit_log']) {
            // 只能查看自己的回访记录
            $map[] = ['user_id', '=', $user['id']];
        }
        $map[] = ['member_id', '=', $get['id']];
        $visits = model('MemberVisit')->where($map)->select()->toArray();
        foreach ($visits as &$value) {
            $time = strtotime($value['create_time']);
            $value['create_time'] = date('y/m/d H:i', $time);
            $value['status'] = $intentions[$value['status']]['title'];
        }
        $this->assign('visits', $visits);
        return $this->fetch();
    }

    /**
     * 创建订单
     * @return mixed
     */
    public function createOrder()
    {
        return $this->fetch('edit_order');
    }
}