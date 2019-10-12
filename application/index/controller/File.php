<?php
namespace app\index\controller;

use app\common\model\Member;
use app\common\model\UploadCustomerLog;

class File extends Base
{
    public function upload()
    {
        $file = request()->file("file");
        $info = $file->move("../uploads");
        if ($info) {
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
            $UploadCustomerLog = new UploadCustomerLog();
            $UploadCustomerLog->insert($data);
            echo $UploadCustomerLog->getLastSql();

            return json(['code' => '200', 'msg' => '上传成功,请继续分配']);
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
                $repetitive[] = $originMember->toArray();
            } else {
                $customer[] = $row;
            }
        }
        fclose($fp);

        return [$customer, $repetitive];
    }

}