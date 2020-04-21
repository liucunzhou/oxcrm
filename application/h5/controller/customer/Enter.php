<?php

namespace app\h5\controller\customer;


use app\common\model\MemberEnter;
use app\h5\controller\Base;
use app\common\model\Member;
use app\common\model\MemberVisit;
use app\common\model\MemberAllocate;
use think\Db;

class Enter extends Base
{

    protected $model = null;
    protected $memberModel = null;

    protected function initialize()
    {
        $this->model = new MemberEnter();
        $this->memberModel = new Member();
    }

    # id = 客资分配id
    public function index()
    {
        $request = $this->request->param();
        $allocate = MemberAllocte::get($request['id']);

        $map = [];
        $map[] = ['user_id', '=', $this->user['id']];
        $map[] = ['member_id', '=', $allocate->member_id];
        $list = $this->model->where($map)->order('create_time desc')->select();
        if( $list ){
            $result = [
                'code'  =>   '200',
                'msg'   =>   '获取成功',
                'data'  =>  $list
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
        $member = Member::get($allocate->member_id);
        $member->startTrans();
        $model = new MemberVisit();
        ### 保存回访信息
        $model->status = $request['status'];
        $model->member_allocate_id = $allocate->id;
        $model->member_id = $allocate->member_id;
        $model->user_id = $this->user['id'];
        $result = $model->save($request);
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
}