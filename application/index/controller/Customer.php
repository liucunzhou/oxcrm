<?php
namespace app\index\controller;

use app\index\model\Csv;
use app\index\model\Hotel;
use app\index\model\Intention;
use app\index\model\Member;
use app\index\model\MemberAllocate;
use app\index\model\MemberApply;
use app\index\model\MemberVisit;
use app\index\model\Store;
use app\index\model\User;
use app\index\model\UserAuth;
use think\facade\Request;
use think\facade\Session;

class Customer extends Base
{
    public function index()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            ### 获取用户列表
            $users = User::getUsers();
            $sources = \app\index\model\Source::getSources();
            $intentions = Intention::getIntentions();
            $hotels = Hotel::getHotels();
            $newsTypes = ['婚宴信息', '婚庆信息', '婚宴转婚庆'];
            $publishStatus = ['未发布', '已发布'];

            $config = [
                'page' => $get['page']
            ];
            $list = model('Member')->paginate($get['limit'], false, $config);
            $result = [
                'code'  => 0,
                'msg'   => '获取数据成功',
                'count' => $list->total(),
                'data'  => $list->getCollection()
            ];
            return json($result);

            $user = Session::get("user");
            $auth = UserAuth::getUserLogicAuth($user['id']);
            if (empty($auth['role_ids'])) return $this->fetch();

            $roles = explode(',', $auth['role_ids']);
            ### 根据角色自动判断条件
            if (in_array(3, $roles)) {
                ## 客服主管
                $map[] = ['manager_id', '=', $user['id']];
                $this->assign('managerBtn', '');
                $this->assign('staffBtn', 'hide');
                $this->assign('saleBtn', 'hide');
            } else if (in_array(1, $roles)) {
                ## 客服
                $map[] = ['customer_staff_id', '=', $user['id']];
                $this->assign('managerBtn', 'hide');
                $this->assign('staffBtn', '');
                $this->assign('saleBtn', 'hide');
            } else if (in_array(5, $roles)) {
                ## 门店店长
                // $map[] = ['store_id', ''];
                $this->assign('managerBtn', 'hide');
                $this->assign('staffBtn', 'hide');
                $this->assign('saleBtn', '');
            } else if (in_array(4, $roles)) {
                $map[] = ['sale_id', '=', $user['id']];
                $this->assign('managerBtn', 'hide');
                $this->assign('staffBtn', 'hide');
                $this->assign('saleBtn', '');
            }
        } else {
            $this->view->engine->layout(false);
            return $this->fetch();
        }
    }

    public function mine()
    {
        $user = Session::get("user");
        $auth = UserAuth::getUserLogicAuth($user['id']);
        // if(empty($auth['role_ids'])) return $this->fetch();

        $roles = explode(',', $auth['role_ids']);
        ### 根据角色自动判断条件
        if (in_array(3,$roles)) {
            ## 客服主管
            $map[] = ['manager_id', '=', $user['id']];
            $this->assign('managerBtn', '');
            $this->assign('staffBtn', 'hide');
            $this->assign('saleBtn', 'hide');
        } else if(in_array(1, $roles)) {
            ## 客服
            $map[] = ['customer_staff_id', '=', $user['id']];
            $this->assign('managerBtn', 'hide');
            $this->assign('staffBtn', '');
            $this->assign('saleBtn', 'hide');
        } else if(in_array(5, $roles)) {
            ## 门店店长
            // $map[] = ['store_id', ''];
            $this->assign('managerBtn', 'hide');
            $this->assign('staffBtn', 'hide');
            $this->assign('saleBtn', '');
        } else if(in_array(4, $roles)) {
            ## 门店销售
            // $map[] = ['sale_id', '=', $user['id']];
            $this->assign('managerBtn', 'hide');
            $this->assign('staffBtn', 'hide');
            $this->assign('saleBtn', '');
        }

        // $map[] = ['operate_id', '=', $user['id']];
        // $list = model('MemberAllocate')->where($map)->with('member')->paginate(15);
        // $data = $list->getCollection();
        print_r($data);

        // $this->assign('list', $list);
        // return $this->fetch();
        return false;
    }

    public function apply()
    {
        $user = Session::get("user");
        $auth = UserAuth::getUserLogicAuth($user['id']);
        if(empty($auth['role_ids'])) return $this->fetch();

        $roles = explode(',', $auth['role_ids']);
        ### 根据角色自动判断条件
        if (in_array(3,$roles)) {
            ## 客服主管
            $map[] = ['manager_id', '=', $user['id']];
        } else if(in_array(1, $roles)) {
            ## 客服
            $map[] = ['customer_staff_id', '=', $user['id']];
        } else if(in_array(5, $roles)) {
            ## 门店店长
            // $map[] = ['store_id', ''];
        } else if(in_array(4, $roles)) {
            $map[] = ['sale_id', '=', $user['id']];
        }
        $list = model('MemberApply')->where($map)->with('member')->paginate(15);

        $this->assign('list', $list);
        return $this->fetch();
    }

    public function import()
    {
        if (!empty($_FILES)) {
            $file = request()->file("csv");
            $info = $file->move("../uploads");
            if ($info) {
                $fileName = $info->getPathname();
                $data = Csv::readCsv($fileName);
                $this->assign('data', $data[0]);
                // echo "<pre>";

                $user = Session::get("user");
                $hashKey = "batch_upload:" . $user['id'];
                $cacheData = [
                    'file' => $fileName,
                    'amount' => count($data[0]),
                    'repeat' => count($data[1])
                ];
                redis()->hMset($hashKey, $cacheData);
                $this->assign('file', $cacheData);
            } else {
                $this->assign('err', $file->getError());
            }
        }

        return $this->fetch();
    }


    public function addCustomer()
    {
        ### 获取用户列表
        $users = User::getUsers();
        $this->assign('users', $users);

        ### 客资来源
        $sources = \app\index\model\Source::getSources();
        $this->assign('sources', $sources);

        ### 跟单状态
        $intentions = Intention::getIntentions();
        $this->assign('intentions', $intentions);

        ### 酒店列表
        $hotels = Hotel::getHotels();
        $this->assign("hotels", $hotels);

        $this->view->engine->layout(false);
        return $this->fetch('edit_customer');
    }

    public function editCustomer()
    {
        $get = Request::param();
        ### 获取用户列表
        $users = User::getUsers();
        $this->assign('users', $users);

        $sources = \app\index\model\Source::getSources();
        $this->assign('sources', $sources);

        $intentions = Intention::getIntentions();
        $this->assign('intentions', $intentions);

        ### 酒店列表
        $hotels = Hotel::getHotels();
        $this->assign("hotels", $hotels);

        $data = \app\index\model\Member::get($get['id']);
        $this->assign('data', $data);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

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

        // $Model::create($post);
        $checked1 = $Model::checkMobile($post['mobile']);
        if ($checked1) {
            return json([
                'code' => '400',
                'msg' => $post['mobile'] . '手机号已经存在',
            ]);
        }

        $checked2 = $Model::checkMobile($post['mobile1']);
        if ($checked2) {
            return json([
                'code' => '400',
                'msg' => $post['mobile1'] . '手机号已经存在',
            ]);
        }

        $result = $Model->save($post);
        if ($result) {
            ### 更新手机号缓存
            // \app\index\model\Customer::updateCache();
            $Model::pushMoblie($post['mobile']);
            $Model::pushMoblie($post['mobile1']);
            return json(['code' => '200', 'msg' => $action . '成功']);
        } else {
            return json(['code' => '500', 'msg' => $action . '失败']);
        }
    }

    ### 分配到主管
    public function allocate()
    {
        $users = User::getUsers();
        $this->assign('users', $users);

        $user = Session::get("user");
        $hashKey = "batch_upload:" . $user['id'];
        $fileData = redis()->hMGet($hashKey, ['file', 'amount']);
        $this->assign('fileData', $fileData);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    #### 分配到主管
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
                $AllocateModel = new MemberAllocate();
                $data = [];
                $data['operate_id'] = $operateId;
                $data['manager_id'] = $k;
                $data['member_id'] = $member[$start];
                $AllocateModel->insert($data);
            }
            $startArr[] = $v;
        }

        return json([
            'code' => '200',
            'msg' => '录入数据成功'
        ]);
    }

    ### 分发到客服
    public function distribute()
    {
        $users = User::getUsers();
        $this->assign('users', $users);

        // $ids = Request::header("ids");
        $this->view->engine->layout(false);
        return $this->fetch();
    }

    ### 执行分发
    public function doDistribute()
    {
        $post = Request::post();
        $ids = explode(',', $post['ids']);

        foreach ($ids as $id) {
            $Model = new MemberAllocate();
            $map = [];
            $map[] = ['id', '=', $id];
            $Model->save(['customer_staff_id'=>$post['staff']], $map);
        }

        return json([
            'code' => '200',
            'msg' => '分发到二级客服成功'
        ]);
    }

    ### 分发到门店
    public function toStore()
    {
        // $stores = Store::getStoresGroupByBrand();
        // echo "<pre>";
        $stores = Store::getStoreList();
        // print_r($stores);
        $this->assign('stores', $stores);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doToStore()
    {
        $post = Request::post();
        $ids = explode(',', $post['ids']);

        foreach ($ids as $id) {
            $Model = new MemberAllocate();
            $map = [];
            $map[] = ['id', '=', $id];
            $Model->save(['store_id'=>$post['store_id']], $map);
        }

        return json([
            'code' => '200',
            'msg' => '分发到门店成功'
        ]);
    }

    ### 删除客资
    public function delete()
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

    public function doApply()
    {
        $ids = Request::header("ids");
        $ids = explode(',', $ids);
        foreach ($ids as $id) {
            $allocate = MemberAllocate::get($id)->toArray();
            $allocate['member_allocate_id'] = $allocate['id'];
            unset($allocate['id']);
            unset($allocate['create_time']);
            unset($allocate['update_time']);
            unset($allocate['delete_time']);
            $Model = new MemberApply();
            $user = Session::get("user");
            $allocate['sale_id'] = $user['id'];
            $Model->insert($allocate);
        }

        return json([
            'code' => '200',
            'msg' => '申请成功'
        ]);
    }

    public function visit()
    {
        $get = Request::param();
        $map[] = ['member_id', '=', $get['id']];
        $visits = model('MemberVisit')->where($map)->select()->toArray();
        $this->assign('visits', $visits);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doVisit()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '编辑回访成功';
            $Model = \app\index\model\MemberVisit::get($post['id']);
        } else {
            $action = '添加回访成功';
            $Model = new \app\index\model\MemberVisit();
        }

        $user = Session::get("user");
        $Model->user_id = $user['id'];
        $Model->next_visit_time = strtotime($post['next_visit_time']);

        // $Model::create($post);
        $result = $Model->save($post);
        if($result) {
            // empty($post['id']) && $post['id'] = $Model->id;
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    ### 回访记录
    public function logs()
    {
        $get = Request::param();
        $map[] = ['member_id', '=', $get['id']];
        $visits = model('MemberVisit')->where($map)->select()->toArray();
        $this->assign('visits', $visits);

        $this->view->engine->layout(false);
        return $this->fetch();
    }
}