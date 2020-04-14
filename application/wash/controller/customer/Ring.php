<?php
namespace app\wash\controller\customer;

use app\wash\controller\Backend;

class Ring extends Backend {

    public function call()
    {
        $params = $this->request->param();

        $user = session("user");
        $rongModel = new \app\common\model\Rong();

        $customerModel = new \app\common\model\MemberAllocate();
        $customer = $customerModel->where('id', '=', $params['id'])->find();

        $data = [];
        $data['user_id'] = $user['id'];
        $data['create_time'] = time();
        if($params['from'] == 'mobile') {
            // echo $customer->mobile.'-------';
            $result = $rongModel->call($user['mobile'], $customer->mobile);
            // $result = $rongModel->call('18321277411', '13764570091');
            $data['type'] = 'mobile';
            $data['seat'] = $user['mobile'];
        } else {
            $result = $rongModel->call($user['telephone'], $customer->mobile);
            // $result['telephone'] = $user['telephone'];
            // $result['mobile'] = $customer->mobile;
            $data['type'] = 'telephone';
            $data['seat'] = $user['telephone'];
        }

        if($result['Flag'] == 1) {
            // 打电话数据库
            $data['sessionId'] = $result['Msg'];
            $callLog = new \app\common\model\CallLog();
            $callLog->save($data);
        }

        return $result;
    }
}