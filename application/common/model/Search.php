<?php
namespace app\common\model;

class Search
{
    public static function customerMine(&$user, &$get)
    {
        if(isset($get['status']) && $get['status'] == '-1') {
            unset($get['status']);
        }

        if (isset($get['status'])) {
            $map[] = ['active_status', '=', $get['status']];
        }

        if (isset($get['active_assign_status'])) {
            $map[] = ['active_assign_status', '=', $get['active_assign_status']];
        }

        if (isset($get['is_into_store']) && $get['is_into_store'] > 0) {
            $map[] = ['is_into_store', '=', 1];
        }

        if (isset($get['possible_assign_status'])) {
            $map[] = ['possible_assign_status', '=', $get['possible_assign_status']];
        }

        if (isset($get['source']) && $get['source'] > 0) {
            $map[] = ['source_id', '=', $get['source']];
        }

        if (isset($get['staff']) && $get['staff'] > 0) {
            $map[] = ['user_id', '=', $get['staff']];
        }

        switch ($user['role_id']) {
            case 1: // 管理员

            case 15: // 客服经理
                if(!empty($get['staff']) && $get['staff'] = 'all') {
                    $map[] = self::getUserStaffs($user);
                } else {
                    $map[] = ['user_id', '=', $user['id']];
                }
                break;
            case 7: // 洗单组主管
                if(!empty($get['staff']) && $get['staff'] = 'all') {
                    $map[] = self::getUserStaffs($user);
                } else {
                    $map[] = ['user_id', '=', $user['id']];
                }
                break;
            case 2: // 洗单组客服
                $map[] = ['user_id', '=', $user['id']];
                break;
            case 3: // 推荐组主管
                $map[] = self::getUserStaffs($user);
                break;
            case 4: // 推荐组客服
                if(!isset($get['staff']) || empty($get['staff']))  $map[] = ['user_id', '=', $user['id']];
                break;
            case 10: // 派单组主管
                if(!empty($get['staff']) && $get['staff'] = 'all') {
                    $map[] = self::getUserStaffs($user);
                } else {
                    $map[] = ['user_id', '=', $user['id']];
                }
                break;
            case 11: // 派单组客服
                $map[] = ['user_id', '=', $user['id']];
                break;
            case 5: // 门店主管
                if(!empty($get['staff']) && $get['staff'] = 'all') {
                    $map[] = self::getUserStaffs($user);
                } else {
                    $map[] = ['user_id', '=', $user['id']];
                }
                break;
            case 6:
                if(!empty($get['staff']) && $get['staff'] = 'all') {
                    $map[] = self::getUserStaffs($user);
                } else {
                    $map[] = ['user_id', '=', $user['id']];
                }
                break;
            case 8: // 门店销售
                $map[] = ['user_id', '=', $user['id']];
                break;

            case 9: // 商务会员
                $map[] = ['user_id', '=', $user['id']];
                break;

            default :
                $map[] = ['user_id', '=', $user['id']];
                break;
        }

        if(isset($get['create_time']) && !empty($get['create_time'])) {
            $range = self::getDateRange($get['create_time']);
            $map[] = ['create_time', 'between', $range];
        }

        if(isset($get['next_visit_time']) && !empty($get['next_visit_time'])) {
            $range = self::getDateRange($get['next_visit_time']);
            $map[] = ['next_visit_time', 'between', $range];
        }

        return $map;
    }

    public static function order(&$user, &$get)
    {
        $map[] = ['news_type', '=', $get['news_type']];
        if (isset($get['source'])) {
            $map[] = ['source_id', '=', $get['source']];
        }

        if (isset($get['staff'])) {
            $map[] = ['user_id', '='. $get['staff']];
        }

        if (isset($get['date_range']) && !empty($get['date_range'])) {
            $range = self::getDateRange($get['next_visit_time']);
            $map[] = ['create_time', 'between', $range];
        }

        switch ($user['role_id']) {
            case 1: // 管理员
            case 15: // 客服经理
                break;
            case 7: // 洗单组主管
                $map[] = self::getUserStaffs($user);
                break;
            case 2: // 洗单组客服
                $map[] = ['user_id', '=', $user['id']];
                break;
            case 3: // 推荐组主管
                if(!isset($get['staff'])) $map[] = self::getUserStaffs($user);
                break;
            case 4: // 推荐组客服
                $map[] = ['user_id', '=', $user['id']];
                break;
            case 10: // 派单组主管
                if(!isset($get['staff'])) $map[] = self::getUserStaffs($user);
                break;
            case 11: // 派单组客服
                $map[] = ['user_id', '=', $user['id']];
                break;
            case 5: // 门店主管
                if(!isset($get['staff'])) $map[] = self::getUserStaffs($user);
                break;

            case 8: // 门店销售
                $map[] = ['user_id', '=', $user['id']];
                break;

            case 9: // 商务会员
                $map[] = ['user_id', '=', $user['id']];
                break;

            default :
                $map = [];
                $Model = [];
                break;
        }

        return $map;
    }

    public static function getUserStaffs($user) {
        if(!empty($user['department_id'])) {
            $staffs = User::getUsersByDepartmentId($user['department_id']);
            if($staffs) {
                $map = ['user_id', 'in', $staffs];
            } else {
                $map = ['user_id', '=', $user['id']];
            }
        } else {
            $map = ['user_id', '=', $user['id']];
        }

        return $map;
    }

    public static function formatDateRange($range)
    {
        switch($range) {
            case 'today' :
                // 今天
                break;
            case 'tomorrow':
                // 明天
                break;
            case 'this_week';
                // 本周
                break;
            case 'this_month':
                // 本月
                break;
            case stripos($range, '~') > 0 :
                // 自定义区间
                break;
        }
    }

    public static function getDateRange($dateRange) {
        if($dateRange == 'today') {

            $start = strtotime(date('Y-m-d'));
            $end = strtotime('tomorrow');

        } else {

            $range = explode('~', $dateRange);
            $range[0] = trim($range[0]);
            $range[1] = trim($range[1]);
            $start = strtotime($range[0]);
            $end = strtotime($range[1]) + 86400;
        }

        return [$start, $end];
    }
}