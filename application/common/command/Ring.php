<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/9/17
 * Time: 6:48 PM
 */

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Ring extends Command
{
    protected function configure()
    {
        $this->setName("Ring")
            ->addOption('action', null, Option::VALUE_REQUIRED, '要执行的动作')
            ->addOption('start', null, Option::VALUE_OPTIONAL, '开始日期')
            ->addOption('end', null, Option::VALUE_OPTIONAL, '开始日期')
            ->addOption('maxId', null, Option::VALUE_OPTIONAL, '最大号');
    }

    protected function execute(Input $input, Output $output)
    {
        $action = '';
        if ($input->hasOption("action")) {
            $action = $input->getOption("action");
        } else {
            $output->writeln("请输入要执行的操作");
            return false;
        }

        if ($input->hasOption('start')) {
            $start = $input->getOption("start");
        } else {
            $start = strtotime('yesterday');
        }

        if ($input->hasOption('end')) {
            $end = $input->getOption("end");
        } else {
            $end = strtotime('tomorrow');
        }

        if ($input->hasOption('maxId')) {
            $maxId = $input->getOption("maxId");
        } else {
            $maxId = 0;
        }

        switch ($action) {
            // 门店未回访提醒
            case 'initRecordList':
                $this->initRecordList($start, $end, 0);
                break;

            // 同步录音
            case 'initRecordVoice':
                $this->initRecordVoice($start, $end);
                break;

            case 'initLogList':
                $this->initLogList();
                break;
        }
    }

    /**
     * 初始化呼叫详情
     */
    public function initRecordList($startTime, $endTime, $maxId)
    {
        $callLog = new \app\common\model\CallLog();
        $rongModel = new \app\common\model\Rong();
        $result = $rongModel->getRecordList($startTime, $endTime, $maxId);

        $len = count($result['Data']);
        echo $len;
        echo "\n";
        if($len > 0) {
            foreach ($result['Data'] as $key=>$row) {
                $where = [];
                $where['sessionId'] = $row['sessionId'];
                $log = $callLog->where($where)->find();
                if($log) {
                    $row['userid'] = $log->user_id;
                }

                $callRecord = new \app\common\model\CallRecord();
                $rs = $callRecord->insert($row);
                if(!$rs) {
                    echo "写入失败\n";
                    echo $callRecord->getLastSql();
                    echo "\n";
                }

                if($len-1==$key) {
                    $maxId = $row['maxid'];
                    $this->initRecordList($startTime, $endTime, $maxId);
                }
            }
        }
    }


    /**
     * 初始化initCall
     */
    public function initRecordVoice($startDate, $endDate)
    {
        $streamOpts = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ]
        ];

        $callRecord = new \app\common\model\CallRecord();
        $where = [];
        $where[] = ['fwdStartTime', 'between', [$startDate, $endDate]];
        $list = $callRecord->where($where)->select();

        foreach($list as $key=>$row) {
            if(empty($row->recordFileDownloadUrl)) continue;

            $url = $row->recordFileDownloadUrl;
            // $wav = file_get_contents($url, false, stream_context_create($streamOpts));
            $wav = $this->getCurl($url);
            $date = substr($row->fwdStartTime, 0, 10);
            $dir = "./public/record/{$date}/";
            if(!is_dir($dir)) mkdir($dir, 0755);

            $result = file_put_contents($dir.$row->sessionId.'.wav', $wav);
            if($result) {
                echo "获取{$row->sessionId}成功\n";
                $row->save(['isDownload'=>1]);
            } else {
                echo "获取{$row->sessionId}失败\n";
            }
            sleep(2);
        }
    }

    /**
     * 根据初始化好的呼叫详情绑定电话记录
     */
    public function initLogList()
    {
        $callRecord = new \app\common\model\CallRecord();
        $list = $callRecord->select();
        foreach($list as $row) {
            $mobile = substr($row->calleeNum, 3);
            $where = [];
            $where['mobile'] = $mobile;
            $user = \app\common\model\User::where($where)->find();

            $callLog = new \app\common\model\CallLog();
            $where = [];
            $where['sessionId'] = $row->sessionId;
            $log = $callLog->where($where)->find();
            $data = [];
            $data['user_id'] = $user->id;
            $data['sessionId'] = $row->sessionId;
            $data['type'] = 'mobile';
            $data['seat'] = $mobile;
            $data['create_time'] = time();
            if(empty($log)) {
                $callLog->save($data);
            } else {
                $log->save($data);
            }

            if(empty($row->userid)) {
                $data = [];
                $data['userid'] = $user->id;
                $row->save($data);
            }

            echo $user->id.":::{$mobile}同步成功\n";
        }
    }

    protected function getCurl($url){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        curl_close ($ch);

        return $result;
    }
}