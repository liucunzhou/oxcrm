<?php
namespace app\common\model;


class Csv
{
    public static function readCsv($csv_file = '')
    {
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

            $originMember = Member::checkMobile($row[1]);
            if(!empty($originMember)) {
                $repetitive[] = $originMember->toArray();
            } else {
                $customer[] = $row;
            }
        }
        fclose($fp);

        return [$customer, $repetitive];
    }

    public static function exportCsv($csv_file='', $new_csv_file='')
    {
        if (!$fp = fopen($csv_file, 'r')) {
            return false;
        }

        $sourcesIndexOfId = Source::getSources();
        $sources = Source::getSourcesIndexOfTitle();
        $user = session("user");
        $nfp = fopen($new_csv_file, 'w+');
        $n = 0;
        while (!feof($fp)) {
            $row = fgetcsv($fp);
            if(!$row) break;

            if ($n==0) {
                $n = $n + 1;
                fputcsv($nfp, $row);
            } else {
                $originMember = Member::checkMobile($row[1]);
                //print_r($originMember);
                if ($originMember) {
                    $sourceText = mb_convert_encoding($row[2], 'UTF-8', 'GBK');
                    $sourceId = $sources[$sourceText];
                    Member::updateRepeatLog($originMember, $sourceId, $user, $sourcesIndexOfId);
                    $originSourceText = mb_convert_encoding($originMember->source_text, 'GBK');
                    $repeatLog = mb_convert_encoding($originMember->repeat_log, 'GBK');
                    $repeatLog = str_replace(',',":::", $repeatLog);
                    $row[4] = $originSourceText.':::'.$repeatLog;
                    // $row[4] = $originMember->source_text.':::'.str_replace(",",":::", $originMember->repeat_log);
                    fputcsv($nfp, $row);
                }
            }
        }
        fclose($nfp);
        fclose($fp);

        return $new_csv_file;
    }
}