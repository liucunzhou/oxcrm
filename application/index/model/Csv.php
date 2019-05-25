<?php
namespace app\index\model;


class Csv
{
    public static function readCsv($csv_file = '')
    {
        if (!$fp = fopen($csv_file, 'r')) {
            return false;
        }

        $repetitive = [];
        $data = [];
        while (!feof($fp)) {
            $row = fgetcsv($fp);
            // $isExist = Member::checkMobile($row[1]);
            if(false) {
                $repetitive[] = $row;
            } else {
                $data[] = $row;
                ### 写入到手机号列表
                // Member::pushMoblie($row[1]);
            }
        }
        fclose($fp);

        return [$data, $repetitive];
    }
}