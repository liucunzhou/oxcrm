<?php

namespace app\admin\controller\store;

use app\admin\controller\Backend;
use think\Request;
use app\common\model\User;
use app\common\model\Store;

class Staff extends Backend
{
    protected $users =  [];
    protected $stores = [];
    protected $params = [];

    protected function initialize()
    {
        parent::initialize();
        $this->params = $this->request->param();
        $this->assign('params', $this->params);

        $this->model = new \app\common\model\StoreStaff();

        $where = [];
        $where['role_id'] = [5,6,8];
        $this->users = User::where($where)->column('id,nickname,realname', 'id');
        $this->assign('users', $this->users);

        $where = [];
        $this->stores = Store::where($where)->column('id,title,sort', 'id');
        $this->assign('stores', $this->stores);

    }

    /**
     * 显示绑定的销售列表
     *
     * @return \think\Response
     */
    public function staffs()
    {
        $params = $this->request->param();
    
        $where = [];
        if(isset($params['store_id'])) {
            $where['store_id'] = $params['store_id'];
        }
        $list = $this->model->where($where)->paginate(15);
        $this->assign('list', $list);

        return $this->fetch();
    }

    /**
     * 显示绑定的销售列表
     *
     * @return \think\Response
     */
    public function stores()
    {
        $params = $this->request->param();
    
        $where = [];
        if(isset($params['staff_id'])) {
            $where['staff_id'] = $params['staff_id'];
        }
        $list = $this->model->where($where)->paginate(15);
        $this->assign('list', $list);

        return $this->fetch();
    }

    /**
     * 绑定销售与酒店关联
     *
     * @return \think\Response
     */
    public function create()
    {
        $store = Store::get($this->params['store_id']);
        $this->assign('store', $store);

        return $this->fetch();
    }


    /**
     * 绑定酒店与销售关联
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function add()
    {
        $user = User::get($this->params['staff_id']);
        $this->assign('user', $user);
        return $this->fetch();
    }

    public function doEdit()
    {
        $where = [];
        $where['store_id'] = $this->params['store_id'];
        $where['staff_id'] = $this->params['staff_id'];
        $obj = $this->model->where($where)->find();
        
        if(!empty($obj)) {
            $arr = [
                'code'  => '500',
                'msg'   => '该员工已经绑定该酒店'
            ];

            return json($arr);
        }

        $result = $this->model->allowField(true)->save($this->params);
        $arr = [
            'code'  => '200',
            'msg'   => '绑定成功',
            'redirect' => $this->params['redirect']
        ];

        return json($arr);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $row = $this->model->find($id);
        $result = $row->delete();
        if ($result) {
            $arr = [
                'code'  => '200',
                'msg'   => '删除成功',
            ];
        } else {
            $arr = [
                'code'  => '500',
                'msg'   => '删除失败',
            ];
        }

        return json($arr);
    }
}
