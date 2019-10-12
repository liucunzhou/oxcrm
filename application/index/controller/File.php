<?php
namespace app\index\controller;

use app\common\model\Member;
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
            $fileData = $this->readCsv($data['new_file_path']);
            $activeAmount = count($fileData[0]);
            $duplicateAmount = count($fileData[1]);
            $data['amount'] = $activeAmount + $duplicateAmount;
            $data['active_amount'] = $activeAmount;
            $data['duplicate_amount'] = $duplicateAmount;
            $data['create_time'] = $time;
            $UploadCustomerFile = new UploadCustomerFile();
            // $UploadCustomerFile->startTrans();
            $UploadCustomerFile->insert($data);
            $uploadId = $UploadCustomerFile->getLastInsID();
            if($uploadId) {
                /**
                | upload_id   | int(11)      | NO   |     | NULL    |                |
                | realname    | varchar(100) | YES  |     | NULL    |                |
                | mobile      | char(20)     | NO   |     | NULL    |                |
                | source_text | char(32)     | NO   |     | NULL    |                |
                | city_text   | char(32)     | NO   |     | NULL    |                |
                | create_time | int(11)      | NO   |     | 0       |                |
                | update_time | int(11)      | NO   |     | NULL    |                |
                | delete_time | int(11)      | NO   |     | NULL    |                |
                | type        | int(11)      | NO   |     | 0       |                |
                | duplicate
                 */
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
                    $UploadCustomerLog = new UploadCustomerLog();
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
                    $data['duplicate'] = $value[4];
                    $data['create_time'] = $time;
                    $data['type'] = 0;
                    $UploadCustomerLog = new UploadCustomerLog();
                    $UploadCustomerLog->insert($data);
                    echo $UploadCustomerLog->getLastSql();
                    echo "\n<br>";
                }

                // $UploadCustomerFile->commit();
                return json(['code' => '200', 'msg' => '上传成功,请继续分配']);
            } else {
                // $UploadCustomerFile->rollback();
                return json(['code' => '500', 'msg' => '上传失败']);
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
            if($position === 0) continue;;

            if(!$row) break;
            ### 手机号
            $row[1] = clear_both_blank($row[1]);
            $originMember = Member::checkPatchMobile($row[1]);
            if(!empty($originMember)) {
                $row[4] = $originMember->source_text.','.$originMember->repeat_log;
                $repetitive[] = $row;
            } else {
                $customer[] = $row;
            }
        }
        fclose($fp);

        return [$customer, $repetitive];
    }

}