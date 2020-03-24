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
                if(!$rs) {
                    echo "写入失败\n";
                    echo $callRecord->getLastSql();
                    echo "\n";
                }

                if($len-1==$key) {
                    $maxId = $row['maxid'];
                    $this->recodeList($startTime, $endTime, $maxId);
                }
            }
        }
    }

    public function initRecordVoice($startDate, $endDate)
    {
        $streamOpts = [
            "ssl" => [
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ]
        ];

        $callRecord = new \app\common\model\CallRecord();
        $where = [];
        $where[] = ['fwdStartTime', 'between', [$startDate, $endDate]];
        $list = $callRecord->where($where)->select();

        foreach($list as $key=>$row) {
            if(empty($row->recordFileDownloadUrl)) continue;

            $url = $row->recordFileDownloadUrl;
            $wav = file_get_contents($url, false, stream_context_create($streamOpts));
            $date = substr($row->fwdStartTime, 0, 10);
            $dir = './record/{$date}/';
            if(!is_dir($dir)) mkdir($dir, 0755);

            file_put_contents($dir.$row->id.'.wav', $wav);
        }
    }
}