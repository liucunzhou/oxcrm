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

        $customerModel = new \app\common\model\Customer();
        $customer = $customerModel->where('id', '=', $params['id'])->find();
        
        if($params['from'] == 'mobile') {
            // $rongModel->call($user['mobile'], $customer->mobile);
            $rongModel->call('18321277411', '13764570091');
        } else {
            $rongModel->call($user['telephone'], $customer->mobile);
        }
    }

    public function center()
    {
        $data = filet_get_input("php://input");

        file_put_contents("./1.txt", $data);
    }
}