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
        $request = $this->request->param();
        $rongModel = new Rong();
        $MemberAllocate = new MemberAllocate();

        ###  查询分配表数据
        $customer = $MemberAllocate->where('id','=',$request['id'])->field('id,member_id,mobile,mobile1')->find();

        ###  查询个人手机号
        $user = User::get($customer['member_id']);

        $result = $rongModel->call($user['mobile'], $customer->$request['type']);
        if($result['Flag'] == 1) {
            // 打电话数据库
            $data['sessionId'] = $result['Msg'];
//            $callLog = new \app\common\model\CallLog();
//            $callLog->save($data);
            $result = [
                'code'  =>  '200',
                'msg'   =>  $result['Msg'],
                'data'  =>  ''
            ];
        } else {
            $result = [
                'code'  =>  '400',
                'msg'   =>  $result['Msg'],
                'data'  =>  ''
            ];
        }

        return json($result);
    }
}