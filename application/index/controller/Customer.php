<?php
namespace app\index\controller;

use app\index\model\Hotel;
use app\index\model\Intention;
use app\index\model\User;
use think\facade\Request;
use think\facade\Session;

class Customer extends Base
{
    public function index()
    {
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

        ### 信息类型
        $newsTypes = ['婚宴信息','婚庆信息','婚宴转婚庆'];
        $this->assign('newsTypes', $newsTypes);

        ### 发布状态
        $publishStatus = ['未发布', '已发布'];
        $this->assign('publishStatus', $publishStatus);

        $list = model('Member')->paginate(10);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function mine()
    {
        return $this->fetch();
    }

    public function favorite(){
        return $this->fetch();
    }

    public function import()
    {
        if(!empty($_FILES)) {
            $file = request()->file("csv");
            $info = $file->move("../uploads");
            if($info) {
                $fileName = $info->getPathname();
                $data = $this->readCsvlines($fileName);
                $this->assign('data', $data);

                $user = Session::get("user");
                $hashKey = "batch_upload:".$user['id'];
                $cacheData = [
                    'file' => $fileName,
                    'amount' => count($data)
                ];
                redis()->hMset($hashKey, $cacheData);
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
        if(empty($post['mobile'])) {
            return json([
                'code'  => '400',
                'msg'   => '手机号不能为空',
            ]);
        }

        if(empty($post['realname'])) {
            return json([
                'code'  => '400',
                'msg'   => '客户称谓不能为空',
            ]);
        }

        if($post['id']) {
            $action = '编辑客资';
            $Model = \app\index\model\Member::get($post['id']);
        } else {
            $action = '添加客资';
            $Model = new \app\index\model\Member();
            $Model->member_no = date('YmdHis').rand(100,999);
        }

        // $Model::create($post);
        $checked1 = $Model::checkMobile($post['mobile']);
        if($checked1) {
            return json([
                'code'  => '400',
                'msg'   => $post['mobile'].'手机号已经存在',
            ]);
        }

        $checked2 = $Model::checkMobile($post['mobile1']);
        if($checked2) {
            return json([
                'code'  => '400',
                'msg'   => $post['mobile1'].'手机号已经存在',
            ]);
        }

        $result = $Model->save($post);
        if($result) {
            ### 更新手机号缓存
            // \app\index\model\Customer::updateCache();
            $Model::pushMoblie($post['mobile']);
            $Model::pushMoblie($post['mobile1']);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }


    public function allocate()
    {
        $users = User::getUsers();
        $this->assign('users', $users);

        $user = Session::get("user");
        $hashKey = "batch_upload:".$user['id'];
        $fileData = redis()->hMGet($hashKey, ['file','amount']);
        $this->assign('fileData', $fileData);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function delete()
    {
        $get = Request::param();
        $result = \app\index\model\Member::get($get['id'])->delete();

        if($result) {
            // 更新缓存
            \app\index\model\Member::updateCache();
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }

    public function visit()
    {

        return $this->fetch();
    }

    protected function readCsvlines($csv_file = '')
    {
        if (!$fp = fopen($csv_file, 'r')) {
            return false;
        }

        $data = [];
        while (!feof($fp)) {
            $data[] = fgetcsv($fp);
        }
        fclose($fp);
        return $data;
    }
}