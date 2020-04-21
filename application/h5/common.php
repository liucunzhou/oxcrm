<?php

if (!function_exists('format_date_range')) {
    function format_date_range($range)
    {
        switch ($range) {
            case strpos($range, '~'):
                $arr = explode('~', $range);
                $start = $arr[0];
                $end = $arr[1];
                break;

            case 'd':
                $start = strtotime('today');
                $end = strtotime('tomorrow');
                break;
            case 'w':
                $start = strtotime('last sunday') + 86400;
                $end = strtotime('tomorrow');
                break;
            case 'm':
                $start = strtotime(date('Y-m-01'));
                $end = strtotime('tomorrow');
                break;

            default:
                $start = 0;
                $end = strtotime('tomorrow');

        }

        return [$start, $end];
    }
}
