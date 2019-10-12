<?php
namespace app\index\controller;

class File extends Base
{
    public function upload()
    {
        $files = get_included_files();
        print_r($files);
        $file = request()->file("file");
        $info = $file->move("../uploads");
        if ($info) {
            $origin = $info->getInfo();
            $data = [];
            $data['origin_file_name'] = $origin['name'];
            $data['new_file_name'] = $info->getFileName();
            $data['new_file_path'] = $info->getPathname();

            return json(['code' => '200', 'msg' => '上传成功,请继续分配']);
        } else {
            return json(['code' => '500', 'msg' => '上传失败']);
        }
    }

    private function readCsv($file)
    {
        if (!$fp = fopen($file, 'r')) {
            return false;
        }

        $repetitive = [];
        $customer = [];
        $n = 0;
        while (!feof($fp)) {
            $row = fgetcsv($fp);
            if ($n==0) {
                $n = $n + 1;
                continue;
            }
            if(!$row) break;

            $row[1] = trim($row[1]);
            $row[1] = preg_replace("/\s(?=\s)/", "", $row[1]);
            $row[1] = preg_replace("/^[(\xc2\xa0)|\s]+/", "", $row[1]);
            $row[1] = preg_replace("/[\n\r\t]/", ' ', $row[1]);

            // $row[1] = intval($row[1]);
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