<?php
namespace app\index\controller;


use think\Controller;
use think\facade\Request;

class Ring extends Controller {
    
    public function call()
    {
        $params = Request::param();

        $user = session("user");
        $rongModel = new \app\common\model\Rong();

        $customerModel = new \app\common\model\Member();
        $customer = $customerModel->where('id', '=', $params['id'])->find();
        
        if($params['from'] == 'mobile') {
            // $rongModel->call($user['mobile'], $customer->mobile);
            $resulet = $rongModel->call('18321277411', '13764570091');
        } else {
            $resulet = $rongModel->call($user['telephone'], $customer->mobile);
        }

    }

    public function center()
    {
        
        $input = file_get_contents("php://input");
        file_put_contents("./1.txt", $input);
        $resulet = json_decode($input, 1);
        if($resulet['Flag'] != 1) {
            return false;
        }

        $data = $resulet['Data'];
        $callRecord = new \app\common\model\CallRecord();
        $callRecord->insert($data);
    }
}