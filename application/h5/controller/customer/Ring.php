<?php

namespace app\h5\controller\customer;

use app\h5\controller\Base;
use app\common\model\User;
use app\common\model\Rong;
use app\common\model\MemberAllocate;


class Ring extends Base
{
    public function call()
    {
        if(empty($this->user['mobile'])) {
            $result = [
                'code'  => '400',
                'msg'   => '您的手机号未绑定，请联系管理员'
            ];

            return json($result);
        }

        $request = $this->request->param();
        $rongModel = new Rong();
        $MemberAllocate = new MemberAllocate();

        ###  查询分配表数据
        $customer = $MemberAllocate->where('id','=',$request['id'])->field('id,member_id,mobile,mobile1')->find();
        $result = $rongModel->call($this->user['mobile'], $customer->$request['type']);
        if($result['Flag'] == 1) {
            $data = [];
            $data['user_id'] = $this->user['id'];
            $data['type'] = 'mobile';
            $data['seat'] = $this->user['mobile'];
            $data['create_time'] = time();
            $data['sessionId'] = $result['Msg'];
            $callLog = new \app\common\model\CallLog();
            $callLog->save($data);

            $result = [
                'code'  =>  '200',
                'msg'   =>  $result['Msg'],
                'data'  =>  ''
            ];
        } else {
            $result = [
                'code'  =>  '400',
                'msg'   =>  $result['Msg'],
            ];
        }

        return json($result);
    }

    public function toCall()
    {
        $request = $this->request->param();
        $MemberAllocate = new MemberAllocate();
        $customer = $MemberAllocate->where('id','=',$request['id'])->field('id,mobile,mobile1')->find();
        if( !empty($customer) ){
            $result = [
                'code'  =>  '200',
                'msg'   =>  '获取手机号成功',
                'data'  =>  [
                    'mobile'    =>  $customer[$request['type']]
                ]
            ];
        } else {
            $result = [
                'code'  =>  '400',
                'msg'   =>  '获取手机号失败',
            ];
        }
        return json($result);
    }
}