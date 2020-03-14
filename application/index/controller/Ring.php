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
            echo $customer->mobile.'-------';
            $result = $rongModel->call($user['mobile'], $customer->mobile);
            // $resulet = $rongModel->call('18321277411', '13764570091');
        } else {
            $result = $rongModel->call($user['telephone'], $customer->mobile);
        }

        return $result;
    }

    public function center()
    {
        
        $input = file_get_contents("php://input");
        if(empty($input)) return json(['code'=>'400', 'msg'=>'请求空数据']);

        file_put_contents("./1.txt", $input);
        $result = json_decode($input, 1);
        if($result['Flag'] != 1) {
            return false;
        }

        $data = $result['Data'];
        $callRecord = new \app\common\model\CallRecord();
        $callRecord->insert($data);
    }
}