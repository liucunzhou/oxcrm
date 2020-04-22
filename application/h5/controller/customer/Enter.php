<?php

namespace app\h5\controller\customer;

use app\common\model\MemberEnter;
use app\common\model\Store;
use app\h5\controller\Base;
use app\common\model\Member;
use app\common\model\MemberVisit;
use app\common\model\MemberAllocate;

class Enter extends Base
{

    protected $model = null;
    protected $memberModel = null;

    protected function initialize()
    {
        parent::initialize();
        $this->model = new MemberEnter();
        $this->memberModel = new Member();
    }

    # id = 客资分配id
    public function index()
    {
        $request = $this->request->param();
        $allocate = MemberAllocate::get($request['id']);

        $map = [];
        $map[] = ['user_id', '=', $this->user['id']];
        $map[] = ['member_id', '=', $allocate->member_id];
        $list = $this->model->where($map)->order('create_time desc')->select();
        if( $list ){
            $result = [
                'code'  =>   '200',
                'msg'   =>   '获取成功',
                'data'  =>  [
                    'enter' =>  $list
                ]
            ];
        } else {
            $result = [
                'code'  =>   '400',
                'msg'   =>   '获取失败'
            ];
        }
        return json($result);
    }

    # id = 客资分批ID
    public function doCreate()
    {
        $request = $this->request->param();
        $allocate = MemberAllocate::get($request['id']);
        unset($request['id']);
        $model = new MemberEnter();

        ### 保存回访信息
        $request['status'] = 0;
        $request['member_allocate_id'] = $allocate->id;
        $request['member_id'] = $allocate->member_id;
        $request['user_id'] = $this->user['id'];
        $result = $model->allowField(true)->save($request);

        if( $result ){
            $result = [
                'code'  =>   '200',
                'msg'   =>    '插入成功'
            ];
        } else {
            $result = [
                'code'  =>   '400',
                'msg'   =>    '插入失败'
            ];
        }
        return json($result);
    }

    public function edit()
    {
        $request = $this->request->param();
        $field = "id,store_id,subscribe_time,real_time,next_time,status,remark,create_time";
        $details = $this->model->where('id' ,'=' ,$request['id'])->field($field)->find();
        $store = Store::getStore($details->store_id);
        $details['store_id'] = $store['title'];

        $intoStatusList = $this->config['into_status_list'];
        $result = [
            'code'  =>  '200',
            'msg'   =>  '进店状态',
            'data'  =>  [
                'details'           =>  $details,
                'into_status_list'  =>  $intoStatusList
            ]
        ];

        return json($result);
    }

    public function doEdit()
    {
        $request = $this->request->param();
        $enter = $this->model->where('id', '=', $request['id'])->find();
        $allocateId = $enter->member_allocate_id;
        $result = $enter->save($request);

        if( $result ){
            if ($request['status'] == 1) {
                MemberAllocate::where('id', '=', $allocateId);
            }

            $result = [
                'code'  =>   '200',
                'msg'   =>    '编辑成功'
            ];
        } else {
            $result = [
                'code'  =>   '400',
                'msg'   =>    '编辑失败'
            ];
        }
        return json($result);
    }
}