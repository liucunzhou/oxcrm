<?php
namespace app\index\model;


class Csv
{
    public static function readCsv($csv_file = '')
    {
        $user = session("user");
        if (!$fp = fopen($csv_file, 'r')) {
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
            $memberedId = Member::checkMobile($row[1]);
            if($memberedId) {
                $repetitive[] = $row;
                $data = [];
                $data['user_id'] = $user['id'];
                $data['member_id'] = $memberedId;
                $data['source_id'] = $row[10];
                $data['create_time'] = time();
                $DuplicateLog = new DuplicateLog();
                $DuplicateLog->insert($data);
            } else {
                $customer[] = $row;
            }
        }
        fclose($fp);

        return [$customer, $repetitive];
    }
}