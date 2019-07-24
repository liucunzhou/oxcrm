<?php
namespace app\index\model;


class Csv
{
    public static function readCsv($csv_file = '')
    {
        if (!$fp = fopen($csv_file, 'r')) {
            return false;
        }

        $data = [];
        while (!feof($fp)) {
            $row = fgetcsv($fp);
            if (empty($row[1])) continue;
            $data[] = $row;
        }
        fclose($fp);

        return $data;
    }
}