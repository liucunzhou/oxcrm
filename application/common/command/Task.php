<?php
namespace app\common\command;

use app\common\model\Member;
use app\common\model\BanquetHall;
use app\common\model\MemberAllocate;
use app\common\model\MemberVisit;
use app\common\model\Store;
use app\common\model\User;
use app\common\model\UserAuth;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Task extends Command
{
    protected function configure()
    {
        $this->setName("Task")
            ->addOption('action', null, Option::VALUE_REQUIRED, '要执行的动作')
            ->addOption('page', null, Option::VALUE_OPTIONAL, '分页');
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

        if ($input->hasOption('page')) {
            $page = $input->getOption("page");
        } else {
            $page = 0;
        }

        switch ($action) {
            case 'member':
                $this->getMember();
                break;
            case 'store':
                $this->getStore();
                break;

            case 'hall':
                $this->getHall();
                break;
            case 'user':
                $this->getUser();
                break;
            case 'client':
                $this->getClient();
                break;
            case 'visit':
                $this->getVisitLog();
                break;
            case 'amount':
                $this->syncCustomerVisitAmount($page);
                break;
            case "assign";
                $this->assignMemberFromVisit();
                break;
            case "member_to_cache";
                $this->initMemberToCache($page);
                break;
        }
    }

    public function initMemberToCache($page)
    {
        $redis = redis();
        $config = [
            'page' => $page
        ];
        $MemberModel = new Member();
        $list = $MemberModel->paginate(10000, false, $config);
        foreach($list as $row) {
            $member = $row->getData();
            $json = json_encode($member);
            $redis->hSet('member_list', $member['member_no'], $json);
        }
    }

    public function assignMemberFromVisit()
    {
        $redis = redis();
        $map = [];
        // $map[] = ['id', '=', 469];
        $users = User::withTrashed()->where($map)->select();
        foreach($users as $user) {
            $where = [];
            if(!empty($user['user_nos'])) {
                $where[] = ['clienter_no', 'in', $user['user_nos']];
            } else {
                $where[] = ['clienter_no', '=', $user['user_no']];
            }
            $visits = MemberVisit::where($where)->select();
            foreach($visits as $visit) {
                $json = $redis->hGet('member_list', $visit['member_no']);
                if(empty($json)) continue;
                $member = json_decode($json, true);
                $data = [];
                $data['user_id'] = $user['id'];
                $data['member_id'] = $member['id'];
                $data['status'] = empty($member['active_status']) ? 0 : $member['active_status'];
                $data['banquet_size'] = empty($member['banquet_size']) ? 0 : $member['banquet_size'];
                $data['budget'] = empty($member['budget']) ? 0 : $member['budget'];
                $data['wedding_date'] = empty($member['wedding_date']) ?  '' : $member['wedding_date'];
                $data['hotel_text'] = $member['hotel_text'];
                $data['zone'] = $member['zone'];
                $visit->save($data);
                echo $visit->getLastSql();
                echo "\n";
                $data['active_status'] = $member['active_status'];
                // MemberAllocate::
                MemberAllocate::updateAllocateData($user['id'], $member['id'], $data);
            }
            echo $user['realname']."的客资同步完成\n";
        }
    }

    /**
     * 获取客户数据
     * 对应dbo.Addclt.Table.sql
     */
    protected function getMember()
    {
        $now = time();
        // $redis = redis();
        $path = "./update/new/tk_member.sql";
        $file = fopen($path, 'r');
        /**
        $point = $redis->get("member-point");
        if($point > 0) {
            fseek($file, $point);
            if(!feof($file)) fgets($file);
        }
         * **/

        $lastLine = '';
        while (!feof($file)) {
            $line = fgets($file);
            $line = trim($line);
            if(empty($line) || $line == 'GO') continue;
            // var_dump($lastLine);
            if(!empty($lastLine)) {
                $line = $lastLine.$line;
                $lastLine = '';
            }

            // var_dump($lastLine);
            // echo "\n";
            $newLine = substr($line, 0, -1);
            $arr = explode(', ', $newLine);
            $len = count($arr);
            // echo $line;
            print_r($arr);
            if($len > 25) {
                $arr = $this->resetArr($arr);
            }

            print_r($arr);
            $len = count($arr);
            if($len > 25) break;

            if($len == 25) {
                $data = [];
                // 0 id
                $data['id'] = $arr[0];

                // 1 realname field1
                $data['realname'] = $this->formatFieldValue($arr[1]);

                // 2 mobile field3
                $data['mobile'] = $this->formatFieldValue($arr[3]);

                // 4 客户编号 field4
                $data['member_no'] = $this->formatFieldValue($arr[4]);

                // 5 发布业务员 field5
                $data['add_user_no'] = $this->formatFieldValue($arr[5]);

                // 6 信息类型 field6
                $data['news_type'] = $arr[6];

                // 10 桌数/规模 field10
                $data['banquet_size'] = $this->formatFieldValue($arr[10]);

                // 11 预算 field11
                $data['budget'] = $this->formatFieldValue($arr[11]);

                // 12 发布状态 field12
                $data['publish_status'] = $arr[12];

                // 13 备注 field13
                $data['remark'] = $this->formatFieldValue($arr[13]);

                // 14 有效状态 field14
                $data['active_status'] = $arr[14];

                // 15 婚期 field15
                $data['wedding_date'] = $this->formatFieldValue($arr[15]);

                // 16 区域 field16
                $data['zone'] = $this->formatFieldValue($arr[16]);

                // 21 hotel_text 咨询酒店 field21
                $data['hotel_text'] = $this->formatFieldValue($arr[21]);

                // 22 渠道 source_text field22
                $data['source_text'] = $this->formatFieldValue($arr[22]);

                // 23 其他联系人 mobile2
                $data['mobile1'] = $this->formatFieldValue($arr[23]);

                // 更新时间
                $data['modify_time'] = $this->convertRawToStr($arr[18]);

                // 添加时间
                $data['create_time'] = $this->convertRawToStr($arr[24]);
                if(!is_numeric($arr[14])) {
                    print_r($arr);
                    error_log($line."\n", 3, 'update/log/tk_member.log');
                } else {
                    $MemberModel = new Member();
                    $result = $MemberModel->insert($data);
                    if ($result) {
                        $point = ftell($file);
                        // $redis->set('member-point', $point);
                        echo "ID:{$data['id']}的客资成功写入\r\n";
                    } else {
                        error_log($line . "\n", 3, 'update/log/tk_member.log');
                    }
                }
            } else {

                $lastLine = str_replace("\n", "", $line);
                error_log($line."\n", 3, 'update/log/tk_member.log');
                continue;
            }
        }
        fclose($file);
    }

    protected function resetArr($arr) {
        $newArr = [];
        foreach($arr as $key=>$val) {
            if(!is_numeric($val) && !empty($val) && strpos($val, 'N')!==0 && strpos($val, 'CAST')!==0) {
                $index = count($newArr) - 1;
                $newArr[$index] = $newArr[$index].$val;
            } else if(strpos($val,'.')> 0 && !empty($val) && strpos($val, 'N')!==0 && strpos($val, 'CAST')!==0) {
                $index = count($newArr) - 1;
                $newArr[$index] = $newArr[$index].$val;
            } else {
                $newArr[] = $val;
            }
        }

        return $newArr;
    }

    /**
     * 获取门店数据
     */
    protected function getStore()
    {
        $now = time();
        $path = "./update/new/tk_store.sql";
        $file = fopen($path, 'r');
        while (!feof($file)) {
            $line = fgets($file);
            $line = substr($line, 0, -1);
            $arr = explode(',', $line);
            $len = count($arr);
            if($len == 16) {
                // 0 id field0
                $data['id'] = $arr[0];

                // 1 title field1
                $data['title'] = $this->formatFieldValue($arr[1]);

                // 3 titlepic field3
                $data['titlepic'] = $this->formatFieldValue($arr[3]);

                // 4 store_no field4
                $data['store_no'] = $this->formatFieldValue($arr[4]);

                // 5 min_price field5
                $data['min_price'] = $arr[5];

                // 6 max_price field6
                $data['max_price'] = $arr[6];

                // 7 star field7
                $data['star'] = $arr[7];

                // 10 address field10
                $data['address'] = $this->formatFieldValue($arr[10]);

                // 12 min_capacity field12
                $data['min_capacity'] = $arr[12];

                // 13 max_capacity field13
                $data['max_capacity'] = $arr[13];

                // 14 is_valid field14
                $data['is_valid'] = $arr[14];

                // 15 create_time field15
                $data['create_time'] = $this->convertRawToStr($arr[15]);


                $StoreModel = new Store();
                $result = $StoreModel->insert($data);
                if ($result) {
                    echo "ID:{$data['id']}的门店信息成功写入\r\n";
                } else {
                    error_log($line . "\n", 3, 'update/log/tk_store.log');
                }

            } else {
                error_log($line."\n", 3, 'update/log/tk_store.log');
                continue;
            }
        }
        fclose($file);
    }

    /**
     * 获取婚宴厅数据
     */
    protected function getHall()
    {
        $path = "./update/new/tk_banquet_hall.sql";
        $file = fopen($path, 'r');
        while (!feof($file)) {
            $line = fgets($file);
            $line = substr($line, 0, -1);
            $arr = explode(',', $line);
            $len = count($arr);
            if($len == 15) {
                // 0 id field0
                $data['id'] = $arr[0];

                // 1 title field1
                $data['title'] = $this->formatFieldValue($arr[1]);

                // 3 titlepic field3
                $data['titlepic'] = $this->formatFieldValue($arr[3]);

                // 4 store_no field4
                $data['hall_no'] = $this->formatFieldValue($arr[4]);

                // 5 store_no field5
                $data['store_no'] = $this->formatFieldValue($arr[5]);

                // 6 min_price field6
                $data['min_price'] = $arr[6];

                // 8 intrc field7
                $data['intrc'] = $this->formatFieldValue($arr[8]);

                // 9 star field7
                $data['status'] = $arr[7];

                // 10 min_capacity field12
                $data['min_capacity'] = $arr[10];

                // 11 max_capacity field13
                $data['max_capacity'] = $arr[11];

                // 12 star field11
                $data['star'] = $arr[12];

                // 13 files fields
                $data['images'] = $arr[13];

                // 14 create_time field14
                $data['create_time'] = $this->convertRawToStr($arr[14]);


                $HallModel = new BanquetHall();
                $result = $HallModel->insert($data);
                if ($result) {
                    echo "ID:{$data['id']}的宴会厅信息成功写入\r\n";
                } else {
                    error_log($line . "\n", 3, 'update/log/tk_banquet_hall.log');
                }

            } else {
                error_log($line."\n", 3, 'update/log/tk_banquet_hall.log');
                continue;
            }
        }
        fclose($file);
    }

    /**
     * 获取系统用户数据
     * dbo.Partner.Table.sql
     */
    protected function getUser()
    {
        $path = "./update/new/tk_user.sql";
        $file = fopen($path, 'r');
        while (!feof($file)) {
            $line = fgets($file);
            $line = trim($line);
            if(empty($line) || $line == 'GO') continue;
            // var_dump($lastLine);
            if(!empty($lastLine)) {
                $line = $lastLine.$line;
                $lastLine = '';
            }

            // var_dump($lastLine);
            // echo "\n";
            $newLine = substr($line, 0, -1);
            $arr = explode(', ', $newLine);
            $len = count($arr);
            // echo $line;
            print_r($arr);
            if($len > 29) {
                $arr = $this->resetArr($arr);
            }

            print_r($arr);
            $len = count($arr);
            if($len > 29) break;

            if($len == 29) {
                // 0 id field0
                // $data['id'] = $arr[0];

                // 1 nickname field1
                $data['nickname'] = $this->formatFieldValue($arr[1]);

                // 3 avatar field3
                $data['avatar'] = $this->formatFieldValue($arr[3]);

                // 4 user_no field4
                $data['user_no'] = $this->formatFieldValue($arr[4]);

                // 5 is_valid field5
                $data['is_valid'] = $arr[5];

                // 6 password field6
                $data['password'] = md5('123456');

                // 7 origin_department_id field7
                $data['origin_department_id'] = $this->formatFieldValue($arr[7]);

                // 9 realname field9
                $data['realname'] = $this->formatFieldValue($arr[9]);

                // 13 mobile field13
                $data['mobile'] = $this->formatFieldValue($arr[13]);

                // 15 family_mobile field15
                $data['family_mobile'] = $this->formatFieldValue($arr[15]);

                $data['sex'] = $arr[22];

                // 24 id_card field24
                $data['id_card'] = $this->formatFieldValue($arr[24]);

                // 28 create_time field28
                $data['create_time'] = $this->convertRawToStr($arr[28]);


                $UserModel = new User();
                $result = $UserModel->insert($data);
                if ($result) {
                    echo "ID:{$data['id']}的用户信息成功写入\r\n";
                } else {
                    error_log($line . "\n", 3, 'update/log/tk_user.log');
                }

            } else {

                $lastLine = str_replace("\n", "", $line);
                error_log($line."\n", 3, 'update/log/tk_user.log');
                continue;
            }
        }
        fclose($file);
    }

    /**
     * 获取客户端ID
     * dbo.Clienter.Table.sql
     */
    protected function getClient()
    {
        $path = "./update/new/tk_clienter.sql";
        $file = fopen($path, 'r');
        while (!feof($file)) {
            $line = fgets($file);
            $line = trim($line);
            if(empty($line) || $line == 'GO') continue;
            // var_dump($lastLine);
            if(!empty($lastLine)) {
                $line = $lastLine.$line;
                $lastLine = '';
            }

            // var_dump($lastLine);
            // echo "\n";
            $newLine = substr($line, 0, -1);
            $arr = explode(', ', $newLine);
            $len = count($arr);
            // echo $line;
            print_r($arr);
            if($len > 44) {
                $arr = $this->resetArr($arr);
            }

            print_r($arr);
            $len = count($arr);
            if($len > 44) break;
            if($len == 44) {
                // 0 id field0
                // $data['id'] = $arr[0];

                // 1 nickname field1
                $data['nickname'] = $this->formatFieldValue($arr[1]);

                // 3 avatar field3
                $data['avatar'] = $this->formatFieldValue($arr[3]);

                // 4 user_no field4
                $data['user_no'] = $this->formatFieldValue($arr[4]);

                $data['password'] = md5('123456');

                // 6 sex field6
                $data['sex'] = $arr[6];

                // 12 realname field12
                $data['realname'] = $this->formatFieldValue($arr[12]);

                // 14 mobile field14
                $data['mobile'] = $this->formatFieldValue($arr[14]);

                // 16 is_valid field16
                $data['is_valid'] = $arr[16];

                // 17 origin_department_id field17
                $data['origin_department_id'] = $this->formatFieldValue($arr[17]);


                // 44 create_time field44
                $data['create_time'] = $this->convertRawToStr($arr[43]);

                $UserModel = new User();
                $result = $UserModel->insert($data);
                if ($result) {
                    echo "ID:{$data['id']}的用户信息成功写入\r\n";
                } else {
                    error_log($line . "\n", 3, 'update/log/tk_clienter.log');
                }

            } else {

                $lastLine = str_replace("\n", "", $line);
                error_log($line."\n", 3, 'update/log/tk_clienter.log');
                continue;
            }
        }
        fclose($file);
    }

    /**
     * dbo.CGMessage.Table.sql
     */
    protected function getVisitLog()
    {
        // $redis = redis();
        $path = "./update/new/tk_visit_log.sql";
        $file = fopen($path, 'r');
        /**
        $point = $redis->get("point");
        if($point > 0) {
            fseek($file, $point);
            if(!feof($file)) fgets($file);
        }
         * **/

        while (!feof($file)) {
            $line = fgets($file);
            $line = trim($line);
            if(empty($line) || $line == 'GO') continue;
            // var_dump($lastLine);
            if(!empty($lastLine)) {
                $line = $lastLine.$line;
                $lastLine = '';
            }

            // var_dump($lastLine);
            // echo "\n";
            $newLine = substr($line, 0, -1);
            $arr = explode(', ', $newLine);
            $len = count($arr);
            // echo $line;
            print_r($arr);
            if($len > 15) {
                $arr = $this->resetArr($arr);
            }

            $len = count($arr);
            if($len > 15) break;

            if($len == 15) {
                if(!is_numeric($arr[0])) {
                    error_log($line . "\n", 3, 'update/log/tk_visit_log.log');
                    continue;
                }

                // 0 id field0
                $data['id'] = $arr[0];

                // 1 content field1
                $data['content'] = $this->formatFieldValue($arr[1]);

                // 4 visit_no field4
                $data['visit_no'] = $this->formatFieldValue($arr[4]);

                // 5 user_no field
                $data['member_no'] = $this->formatFieldValue($arr[5]);

                // 10 clienter_no field10
                $data['clienter_no'] = $this->formatFieldValue($arr[10]);

                // 12 create_time field12
                $data['next_visit_time'] = $this->convertRawToStr($arr[12]);


                // 14 create_time field14
                $data['create_time'] = $this->convertRawToStr($arr[14]);


                $VisitModel = new MemberVisit();
                $result = $VisitModel->insert($data);
                if ($result) {
                    echo "ID:{$data['id']}的回访信息成功写入\r\n";
                    $point = ftell($file);
                    // $redis->set('point', $point);
                } else {
                    error_log($line . "\n", 3, 'update/log/tk_visit_log.log');
                }

            } else {
                $lastLine = str_replace("\n", "", $line);
                error_log($line."\n", 3, 'update/log/tk_visit_log.log');
                continue;
            }
        }
        //$redis->delete('point');
        fclose($file);
    }

    public function syncCustomerVisitAmount($page)
    {
        $config = [
            'page' => $page
        ];
        $MemberModel = new Member();
        $list = $MemberModel->paginate(5000, false, $config);
        if (!empty($list)) {
            $data = $list->getCollection();

            foreach ($data as $key=>$value) {
                $map = [];
                $map['member_no'] = $value['member_no'];
                $MemberVisit = new MemberVisit();
                $count = $MemberVisit->where($map)->count();

                $map = [];
                $map['id'] = $value['id'];
                $MemberModel = Member::get($value['id']);
                $result = $MemberModel->save(['visit_amount'=>$count]);
                if($result) {
                    echo "{$value['id']}同步成功\r\n";
                } else {
                    echo "{$value['id']}同步失败\r\n";
                }
            }
        }
    }

    protected function formatFieldValue($args) {
        $args = trim($args);
        if(strpos($args, 'N') !== 0 || $args == 'NULL') return $args;
        $args = substr($args, 2, -1);

        return $args;
    }

    protected function convertRawToStr($rawDateTime)
    {
        if ($rawDateTime == null || trim($rawDateTime) == '') return false;
        $rawDate = substr($rawDateTime, 6, 18);
        if($rawDate == '0x0000000000000000') return 0;
        $result = $this->getDateTimeStr8Bytes($rawDate);

        return $result;
    }

    /**
     * 示例:'0x0000A76F010F64F4';
     * 前八位的整数代表(不包含0x):代表从1900年到当前时间的天数
     * 后八位的整数代表: 从午夜0点起的毫秒数, 但是php的秒数是从8点开始的 所以要减去8个小时的秒数才能转换
     * @param $rawData
     * @return float|int
     */
    protected function getDateTimeStr8Bytes($rawData)
    {
        $rawData = trim($rawData);

        if($rawData == '0x0000000000000000') return 0;

        if($rawData == '0x0000000000000000') return 0;

        // 1900-01-01 的时间戳
        $start = -2209017600;
        $hex = substr($rawData, 0, 9);    // 4字节表示剩余的部分 faction
        $stamp = hexdec($hex) * 86400;
        $seconds = $start + $stamp;
        if($seconds > 1564850585) {
            $seconds = 1564850585;
        }

        $date = date('Y-m-d', $seconds);;

        // 计算秒数
        $hex = substr($rawData, 9, 8);
        $seconds = (int)(hexdec($hex) / 300) - 28800;
        $time = date('H:i:s', $seconds);

        return strtotime($date.' '.$time);
    }



    public function sendMessage() {
        // echo "start sendMessage";
        $client = new \swoole_client(SWOOLE_TCP | SWOOLE_ASYNC);
        // $client = new \swoole_client(SWOOLE_TCP);
        $client->on("connect", function($cli) {
            echo "abc";
            $result = $cli->send("hello world\n");
            var_dump($result);
        });

        $client->on("receive", function($cli, $data) {
            echo "received: $data\n";
            $cli->send("hello\n");
        });

        $client->on("close", function($cli){
            echo "closed\n";
        });

        $client->on("error", function($cli){
            exit("error\n");
        });

        $client->connect('121.42.184.177', 9501, 0.5);
    }

}