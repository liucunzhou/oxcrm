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

    protected function initialize()
    {
        parent::initialize();
        $params = $this->request->param();
        $this->assign('params', $params);

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
        print_r($user);
        $this->assign('user', $user);
        return $this->fetch();
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
