<?php

namespace app\wash\controller\customer;

use app\common\model\Intention;
use app\common\model\Member;
use app\common\model\Region;
use app\wash\controller\Backend;
use think\Request;

class Customer extends Backend
{
    protected $customerModel;
    protected $regionModel;

    protected function initialize()
    {
        parent::initialize();

        $this->model = new \app\common\model\MemberAllocate();
        $this->customerModel = new Member();
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $params = $this->request->param();
        if(isset($params['status'])) {
            $status = $params['status'];
        } else {
            $status = 0;
        }

        // 获取洗单组的意向列表
        $intention = new Intention();
        $where['type'] = 'wash';
        $tabs = $intention->where($where)->order('sort desc')->column('id,title,type', 'id');
        $all = [
            'id'    => 0,
            'title' => '所有客资',
            'type'  => 'wash'
        ];
        array_unshift($tabs, $all);

        foreach($tabs as $key=>&$row) {
            // 检测当前
            if ($key == $status) {
                $row['active'] = 1;
            } else {
                $row['active'] = 2;
            }

            $params['status'] = $key;
            $row['url'] = url('customer.customer/index', $params);

            $map = [];
            // 检测所有
            if ($key != 0) {
                $map['active_status'] = $status;
                $total = $this->model->where($map)->count();
            } else {
                $total = $this->model->count(); 
            }
            $row['total'] = $total;
        }
        $this->assign('tabs', $tabs);


        $list = $this->model->order('id desc')->paginate(15);
        $this->assign('list', $list);
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
        // 获取分配信息
        $map = [];
        $map['id'] = $id;
        $allocate = $this->model->where($map)->find();
        $this->assign('allocate', $allocate);

        // 获取客户的基本信息
        $map = [];
        $map['id'] = $allocate->member_id;
        $member = $this->customerModel->where($map)->find();
        $this->assign('member', $member);

        // 获取省市列表
        $provinceList = Region::getProvinceList();
        $this->assign('provinceList', $provinceList);

        return $this->fetch();
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

        return $this->fetch();
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
