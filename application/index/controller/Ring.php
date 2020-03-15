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

        $customerModel = new \app\common\model\MemberAllocate();
        $customer = $customerModel->where('id', '=', $params['id'])->find();
        
        if($params['from'] == 'mobile') {
            // echo $customer->mobile.'-------';
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
        $rs = $callRecord->insert($data[0]);

        $id = $callRecord->getLastInsID();
        $streamOpts = [
            "ssl" => [
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ]
        ];
        $url = $data[0]['recordFileDownloadUrl'];
        $wav = file_get_contents($url, false, stream_context_create($streamOpts));
        $dir = './record/'.date('Ymd').'/';
        if(!is_dir($dir)) {
            mkdir($dir);
        }
        file_put_contents($dir.$id.'.wav', $wav);
    }

    public function recodeList($startTime, $endTime, $maxId=0)
    {
        $rongModel = new \app\common\model\Rong();
        $result = $rongModel->getRecordList($startTime, $endTime, $maxId);

        $len = count($result['Data']);
        echo $len;
        echo "\n";
        if($len > 0) {
            foreach ($result['Data'] as $key=>$row) {
                $callRecord = new \app\common\model\CallRecord();
                $rs = $callRecord->insert($row);
                if($len-1==$key) {
                    $maxId = $row['maxid'];
                    $this->recodeList($startTime, $endTime, $maxId);
                }
            }
        }
    }

    public function initRecordVoice()
    {
        $streamOpts = [
            "ssl" => [
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ]
        ];

        $callRecord = new \app\common\model\CallRecord();
        $list = $callRecord->select();
        foreach($list as $key=>$row) {
            $url = $row->recordFileDownloadUrl;
            $wav = file_get_contents($url, false, stream_context_create($streamOpts));
            file_put_contents('./record/20200314/'.$row->id.'.wav', $wav);
        }
    }
}