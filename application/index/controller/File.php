<?php
namespace app\index\controller;

use app\common\model\Member;
use app\common\model\Region;
use app\common\model\Source;
use app\common\model\UploadCustomerFile;
use app\common\model\UploadCustomerLog;

class File extends Base
{
    public function upload()
    {
        $file = request()->file("file");
        $info = $file->move("../uploads");
        if ($info) {
            $time = time();
            $origin = $info->getInfo();
            $data = [];
            $data['user_id'] = $this->user['id'];
            $data['origin_file_name'] = $origin['name'];
            $data['new_file_name'] = $info->getFileName();
            $data['new_file_path'] = $info->getPathname();
            $readRs = $this->readCsv($data['new_file_path']);
            if(!$readRs['result']) {
                return json([
                    'code'  => '200',
                    'msg'   => $readRs['msg']
                ]);
            } else {
                $fileData = $readRs['data'];
            }
            $activeAmount = count($fileData[0]);
            $duplicateAmount = count($fileData[1]);
            $data['amount'] = $activeAmount + $duplicateAmount;
            $data['active_amount'] = $activeAmount;
            $data['duplicate_amount'] = $duplicateAmount;
            $data['create_time'] = $time;
            $data['hash'] = $info->hash();

            $where = [];
            $where[] = ['hash', '=', $data['hash']];
            $UploadCustomerFileHashed = new UploadCustomerFile();
            $hashed = $UploadCustomerFileHashed->where($where)->find();
            if ($hashed) {
                return json(['code' => '200', 'msg' => '文件已经存在,请勿重复上传']);
            }

            $UploadCustomerFile = new UploadCustomerFile();
            // $UploadCustomerFile->startTrans();
            $UploadCustomerFile->insert($data);
            $uploadId = $UploadCustomerFile->getLastInsID();
            if ($uploadId) {
                ### 记录有效日志
                $UploadCustomerLog = new UploadCustomerLog();
                foreach ($fileData[0] as $value) {
                    $data = [];
                    $data['upload_id'] = $uploadId;
                    $data['realname'] = csv_convert_encoding($value[0]);
                    $data['mobile'] = $value[1];
                    $data['source_text'] = csv_convert_encoding($value[2]);
                    $data['city_text'] = csv_convert_encoding($value[3]);
                    $data['create_time'] = $time;
                    $data['type'] = 1;
                    // $UploadCustomerLog = new UploadCustomerLog();
                    $UploadCustomerLog->insert($data);
                }

                ### 记录重复日志
                // $UploadCustomerLog = new UploadCustomerLog();
                foreach ($fileData[1] as $value) {
                    $data = [];
                    $data['upload_id'] = $uploadId;
                    $data['realname'] = csv_convert_encoding($value[0]);
                    $data['mobile'] = $value[1];
                    $data['source_text'] = csv_convert_encoding($value[2]);
                    $data['city_text'] = csv_convert_encoding($value[3]);
                    if(!empty($value[4])) $data['duplicate'] = $value[4];
                    $data['create_time'] = $time;
                    $data['type'] = 0;
                    // $UploadCustomerLog = new UploadCustomerLog();
                    $UploadCustomerLog->insert($data);
                    // echo $UploadCustomerLog->getLastSql();
                    // echo "\n<br>";
                }

                // $UploadCustomerFile->commit();
                return json(['code' => '200', 'msg' => '上传成功,请继续分配']);
            } else {
                // $UploadCustomerFile->rollback();
                return json(['code' => '500', 'msg' => '上传错误']);
            }
        } else {
            return json(['code' => '500', 'msg' => '上传失败']);
        }
    }

    /**
     * Csv中的数据依次是
     * 客户姓名、联系电话、渠道来源、城市
     * @param $file
     * @return array|bool
     */
    private function readCsv($file)
    {
        $sources = Source::getSourcesIndexOfTitle();
        $cities = Region::getCityListIndexOfShortname();
        if (!$fp = fopen($file, 'r')) {
            return false;
        }

        $repetitive = [];
        $customer = [];
        while (!feof($fp)) {
            ### 读取文件的路径
            $position = ftell($fp);
            ### 读取CSV的行
            $row = fgetcsv($fp);
            if ($position === 0) continue;;
            if (!$row) break;
            ### 检验来源
            $source = csv_convert_encoding($row[2]);
            if(!isset($sources[$source])) {
                return [
                    'result'    => false,
                    'msg'       => '来源'.$source.'不存在'
                ];
            }

            ### 检验城市
            $city = csv_convert_encoding($row[3]);
            if (!isset($cities[$city])) {
                return [
                    'result'    => false,
                    'msg'       => '城市'.$city.'不存在'
                ];
            }

            ### 手机号
            $row[1] = clear_both_blank($row[1]);
            $originMember = Member::checkFromMobileSet($row[1], true);
            if (!empty($originMember)) {
                if (!empty($originMember->repeat_log)) {
                    $duplicate = explode(',', $originMember->repeat_log);
                    $duplicate[] = $source;
                    $duplicate = array_filter($duplicate);
                    $duplicate = array_unique($duplicate);
                } else {
                    $duplicate[] = $source;
                }
                $row[4] = implode(',', $duplicate);
                $repetitive[] = $row;
                $originMember->save(['repeat_log' => $row[4]]);
            } else {
                $customer[] = $row;
            }
        }
        fclose($fp);

        return [
            'result' => true,
            'data' => [$customer, $repetitive]
        ];
    }

}