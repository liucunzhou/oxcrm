<?php
namespace app\index\controller;

use app\index\model\AuthGroup;
use app\index\model\Module;
use app\index\model\OperateLog;
use think\facade\Request;

class Auth extends Base
{
    public function index()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            $modules = Module::getModules();
            $config = [
                'page' => $get['page']
            ];
            $list = model('auth')->order("parent_id,sort desc")->paginate($get['limit'], false, $config);
            $data = $list->getCollection();
            foreach ($data as &$value) {
                $value['parent_id'] = $modules[$value['parent_id']]['name'] ? $modules[$value['parent_id']]['name'] : '系统模块';
                $value['is_valid'] = $value['is_valid'] ? '在线' : '下线';
                $value['is_menu'] = $value['is_menu'] ? '是' : '否';
            }
            $result = [
                'code'  => 0,
                'msg'   => '获取数据成功',
                'count' => $list->total(),
                'data'  => $data
            ];
            return json($result);
        } else {
            return $this->fetch();
        }
    }

    public function addAuth()
    {
        $modules = Module::getModules();
        $this->assign('modules', $modules);

        $this->view->engine->layout(false);
        return $this->fetch('edit_auth');
    }

    public function editAuth()
    {
        $modules = Module::getModules();
        $this->assign('modules', $modules);

        $post = Request::param();
        $data = \app\index\model\Auth::get($post['id']);
        $this->assign('data', $data);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditAuth()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '编辑权限';
            $Model = \app\index\model\Auth::get($post['id']);
        } else {
            $action = '添加权限';
            $Model = new \app\index\model\Auth();
        }

        // $Model::create($post);
        $result = $Model->save($post);

        if($result) {
            empty($post['id']) && $post['id'] = $Model->id;
            ### 更新缓存
            \app\index\model\Auth::updateCache($post['id']);

            ### 添加操作日志
            \app\index\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function deleteAuth()
    {
        $get = Request::param();
        $result = \app\index\model\Auth::get($get['id'])->delete();

        if($result) {
            // 更新缓存
            // \app\index\model\Auth::updateCache($get['id']);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }


    public function group()
    {
        if (Request::isAjax()) {
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];
            $list = model('AuthGroup')->where($map)->order('is_valid desc,sort desc,id asc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection();
            foreach ($data as &$value) {
                $value['is_valid'] = $value['is_valid'] ? '在线' : '下线';
            }
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $data
            ];
            return json($result);
        } else {
            $this->view->engine->layout(false);
            return $this->fetch();
        }
    }

    public function addGroup()
    {

        $this->view->engine->layout(false);
        return $this->fetch('edit_group');
    }

    public function editGroup()
    {
        $get = Request::param();
        $brand = \app\index\model\AuthGroup::get($get['id']);
        $this->assign('data', $brand);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditGroup()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '编辑权限分组';
            $Model = \app\index\model\AuthGroup::get($post['id']);
        } else {
            $action = '添加权限分组';
            $Model = new \app\index\model\AuthGroup();
        }

        // $Model::create($post);
        $result = $Model->save($post);

        if($result) {
            empty($post['id']) && $post['id'] = $Model->getLastInsID();
            ### 更新缓存
            \app\index\model\AuthGroup::updateCache($post['id']);

            ### 添加日志
            \app\index\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function assignAuth()
    {
        $get = Request::param();
        $data = \app\index\model\AuthGroup::getAuthGroup($get['id']);
        $this->assign('data', $data);

        $authSelected = explode(',', $data['auth_set']);
        $this->assign('authSelected', $authSelected);

        $modules = Module::getModules();
        $this->assign('modules', $modules);

        $items = \app\index\model\Auth::getList();
        $this->assign('items', $items);
        return $this->fetch();
    }

    public function doAssignAuth()
    {
        $post = Request::post();
        if(empty($post['id'])) {
            return json([
                'code'  => '400',
                'msg'   => 'id不能为空'
            ]);
        }

        if(empty($post['ids'])) {
            return json([
                'code'  => '400',
                'msg'   => '要分配的权限不能为空'
            ]);
        }

        $ids = implode(',', $post['ids']);
        $Model = AuthGroup::get($post['id']);
        $result = $Model->save(['auth_set'=>$ids]);

        if($result) {
            AuthGroup::updateCache($post['id']);

            ### 添加操作日志
            \app\index\model\OperateLog::appendTo($Model);
            return json([
                'code'  => '200',
                'msg'   => '权限分配成功'
            ]);
        } else {
            return json([
                'code'  => '500',
                'msg'   => '权限分配失败'
            ]);
        }
    }

    public function deleteGroup()
    {
        $get = Request::param();
        $Model = \app\index\model\AuthGroup::get($get['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\index\model\AuthGroup::updateCache($get['id']);

            ### 添加操作日志
            \app\index\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }

    public function modules()
    {

    }

    /**
     * 获取应用下的某个目录下的文件或者文件夹
     * @param string $type model，controller, view, validate
     * @return array
     */
    public function getDirFileList($type='controller')
    {
        exit;
        $path = $this->app->getModulePath().$type;
        $fileNames = [];
        if(is_dir($path)) {
            $dirs = dir($path);
            while (false !== ($entry = $dirs->read())) {
                if($entry == '.' || $entry == '..') continue;
                $fileNames[$entry] = $path.'/'.$entry;
            }
        }

        echo "<pre>";
        // 需要过滤的系统类
        $filters = ['Index','Base','Im','Images','Auth','Tool','System','Passport', 'News', 'Operation', 'Promotion'];
        $ModuleModel = new Module();
        $modules = $ModuleModel->column('name,id');

        $now = time();
        $nodes = [];
        $AuthModel = new \app\index\model\Auth();
        foreach ($fileNames as $key=>$value){
            $actions = $this->getControllerActions($value);
            $module = substr($key, 0, -4);
            if(in_array($module, $filters)) continue;
            $nodes[$module] = $actions;
            $data = [];
            $data['parent_id'] = $modules[$module];
           foreach ($actions as $key=>$action) {
                $data['route'] = $action;
                $data['is_valid'] = 1;
                $data['create_time'] = $now;
                $AuthModel->insert($data);
           }
        }
        print_r($nodes);
        // return $fileNames;
    }

    /***
     * 获取模块下的所有列表
     */
    public function getControllerActions($fileName)
    {
        // 需要过滤的系统方法
        $filters = ['__construct','initialize','beforeAction','fetch','display','assign','filter','engine','validateFailException','validate','success','error','result','redirect','getResponseType'];
        // $fileName = Request::post("fileName");
        $pathinfo = pathinfo($fileName);
        $contorllerName = $pathinfo['filename'];
        $appPath = $this->app->getAppPath();
        $fileName = str_replace($appPath, '', $fileName);
        $fileName = str_replace('/', '\\', $fileName);
        $className = substr($fileName,0, -4);
        $className = "\app\\".$className;
        $class = new $className;
        $functions = get_class_methods($class);
        $data = [];
        foreach ($functions as $val){
            if(in_array($val, $filters)) continue;
            if(substr($val, 0, 2) == 'do') continue;
            $data[$val] = $contorllerName.'/'.$val;
        }

        return $data;
    }
}