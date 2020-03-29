<?php

namespace app\wash\controller\dictionary;

use app\common\model\MemberHotelAllocate;
use app\wash\controller\Backend;
use app\common\model\User;
use think\Request;

class Store extends Backend
{
    protected $customerModel;
    protected $regionModel;
    protected $levels = [];
    protected $users = [];

    protected function initialize(){
        parent::initialize();

        $this->model = new \app\common\model\Store();

        $where = [];
        $where['role_id'] = [5,6,8];
        $this->users = User::where($where)->column('id,nickname,realname', 'id');
        $this->assign('users', $this->users);
    }

    public function search()
    {
        $params = $this->request->param();

        $where = [];
        if(!empty($params['title'])) $where[] = ['title', 'like', "%{$params['title']}%"];
        $list = $this->model->where($where)->select();
        // echo $this->model->getLastSql();
        $this->assign('list', $list);
        
        $where = [];
        $where['id'] = $params['allocate_id'];
        $allocate = \app\common\model\MemberAllocate::where($where)->find();
        $this->assign('allocate', $allocate);

        return $this->fetch();
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $params = $this->request->param();

        // 获取酒店信息
        $where = [];
        $where['id'] = $id;
        $store = $this->model->where($where)->find();
        $this->assign('store', $store);

        if(!empty($store->images)) {
            $images = explode(":::", $store->images);
            foreach($images as &$row) {
                $row = 'https://www.yusivip.com'.$row;
            }
        } else {
            $images = [];
        }
        $this->assign('images', $images);

        // 获取分配数据
        $where = [];
        $where['id'] = $params['allocate_id'];
        $allocate = \app\common\model\MemberAllocate::where($where)->find();
        $this->assign('allocate', $allocate);

        // 获取酒店的客服列表
        $storeStaffModel = new \app\common\model\StoreStaff();
        $where = [];
        $where['store_id'] = $id;
        $storeStaffs = $storeStaffModel->where($where)->column('staff_id');
        $this->assign('storeStaffs', $storeStaffs);

        // 获取已分配的员工
        $where = [];
        $where['member_id'] = $allocate->member_id;
        $allocatedStaff = MemberHotelAllocate::where($where)->column('staff_id');
        $this->assign('allocatedStaff', $allocatedStaff);

        // 获取未分配的员工列表
        $unallocatedStaff = array_diff($storeStaffs, $allocatedStaff);
        $this->assign('unallocatedStaff', $unallocatedStaff);
        return $this->fetch();
    }

}
