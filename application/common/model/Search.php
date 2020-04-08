<?php

namespace app\common\model;

use think\facade\Request;

class Search
{
    // 婚庆组
    public static function customerWeddingMine(&$user, &$get)
    {
        if (isset($get['status']) && $get['status'] == '-1') {
            unset($get['status']);
        }

        if (isset($get['status'])) {
            if ($user['role_id'] == 10 || $user['role_id'] == 11) { // 派单组
                if ($get['status'] == 1) {
                    $map[] = ['active_status', 'in', [1, 5, 6]];
                } else if ($get['status'] == 1) {
                    $map[] = ['active_status', 'in', [3, 4]];
                } else {
                    $map[] = ['active_status', '=', $get['status']];
                }
            } else {
                $map[] = ['active_status', '=', $get['status']];
            }
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

        if (!isset($get['sea']) && isset($get['staff']) && $get['staff'] > 0) {
            $map[] = ['user_id', '=', $get['staff']];
        }

        if (isset($get['allocate_type']) && $get['allocate_type'] > 0) {
            $map[] = ['allocate_type', '=', $get['allocate_type']];
        }

        if (isset($get['city_id']) && $get['city_id'] > 0) {
            $map[] = ['city_id', '=', $get['city_id']];
        }

        switch ($user['role_id']) {
            case 27: // 婚庆部主管
                if (!isset($get['sea']) && !empty($get['staff']) && $get['staff'] == 'all') {
                    $map[] = self::getUserStaffs($user);
                } else if (!isset($get['sea']) && isset($get['staff']) && $get['staff'] > 0) {
                    $map[] = ['user_id', '=', $get['staff']];
                } else if (!isset($get['sea'])) {
                    $map[] = ['user_id', '=', $user['id']];
                }
                break;
            case 28: // 婚庆部客服
                if (empty($get['staff'])) {
                    $map[] = ['user_id', '=', $user['id']];
                }
                break;

            default:
                if (!isset($get['sea'])) {
                    $map[] = ['user_id', '=', $user['id']];
                }
                break;
        }

        if (isset($get['create_time']) && !empty($get['create_time'])) {
            $range = self::getDateRange($get['create_time']);
            $map[] = ['create_time', 'between', $range];
        }

        if (!isset($get['sea']) && isset($get['next_visit_time']) && !empty($get['next_visit_time'])) {
            $range = self::getDateRange($get['next_visit_time']);
            $map[] = ['next_visit_time', 'between', $range];
        }

        return $map;
    }

    public static function customerDispatchMine(&$user, &$get)
    {
        if (isset($get['status']) && $get['status'] == '-1') {
            unset($get['status']);
        }

        if (isset($get['status'])) {
            if ($user['role_id'] == 10 || $user['role_id'] == 11) { // 派单组
                if ($get['status'] == 1) {
                    $map[] = ['active_status', 'in', [0, 1, 5, 6]];
                } else if ($get['status'] == 3) {
                    $map[] = ['active_status', 'in', [3, 4]];
                } else {
                    $map[] = ['active_status', '=', $get['status']];
                }
            } else {
                $map[] = ['active_status', '=', $get['status']];
            }
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

        if (isset($get['allocate_type']) && $get['allocate_type'] > 0) {
            $map[] = ['allocate_type', '=', $get['allocate_type']];
        }

        if (isset($get['city_id']) && $get['city_id'] > 0) {
            $map[] = ['city_id', '=', $get['city_id']];
        }

        switch ($user['role_id']) {
            case 10: // 派单组主管
                if (!isset($get['sea']) && !empty($get['staff']) && $get['staff'] == 'all') {
                    $map[] = self::getUserStaffs($user);
                } else if (!isset($get['sea']) && isset($get['staff']) && $get['staff'] > 0) {
                    $map[] = ['user_id', '=', $get['staff']];
                } else if (!isset($get['sea'])) {
                    $map[] = ['user_id', '=', $user['id']];
                }

                break;
            case 11: // 派单组客服
                if (!isset($get['sea']) && empty($get['staff'])) {
                    $map[] = ['user_id', '=', $user['id']];
                }
                break;

            default:
                if (!isset($get['sea'])) {
                    $map[] = ['user_id', '=', $user['id']];
                }
                break;
        }

        if (isset($get['create_time']) && !empty($get['create_time'])) {
            $range = self::getDateRange($get['create_time']);
            $map[] = ['create_time', 'between', $range];
        }

        if (!isset($get['sea']) && isset($get['next_visit_time']) && !empty($get['next_visit_time'])) {
            $range = self::getDateRange($get['next_visit_time']);
            $map[] = ['next_visit_time', 'between', $range];
        }

        return $map;
    }

    public static function customerMine(&$user, &$get)
    {
        $action = Request::action();
        if (isset($get['status']) && $get['status'] == '-1') {
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

        if (isset($get['allocate_type']) && $get['allocate_type'] > 0) {
            $map[] = ['allocate_type', '=', $get['allocate_type']];
        }

        if (isset($get['city_id']) && $get['city_id'] > 0) {
            $map[] = ['city_id', '=', $get['city_id']];
        }

        /**
        if (isset($get['staff']) && $get['staff'] > 0) {
            $map[] = ['user_id', '=', $get['staff']];
        }
         **/

        switch ($user['role_id']) {
            case 1: // 管理员

            case 15: // 客服经理
                if ($action == 'wash') {
                    ## 回访者
                    if (!empty($get['staff']) && $get['staff'] == 'all') {
                        $staffs = User::getUsersByDepartmentId(14);
                        $map[] = ['user_id', 'in', $staffs];
                    } else if (!empty($get['staff'])) {
                        $map[] = ['user_id', '=', $get['staff']];
                    } else {
                        $map[] = ['user_id', '=', $user['id']];
                    }
                } else if ($action == 'recommend') {
                    ## 回访者
                    if (!empty($get['staff']) && $get['staff'] == 'all') {
                        $staffs = User::getUsersByDepartmentId(15);
                        $map[] = ['user_id', 'in', $staffs];
                    } else if (!empty($get['staff'])) {
                        $map[] = ['user_id', '=', $get['staff']];
                    } else {
                        $map[] = ['user_id', '=', $user['id']];
                    }
                } else {

                    if (!empty($get['staff']) && $get['staff'] == 'all') {
                        $map[] = self::getUserStaffs($user);
                    } else if (!empty($get['staff'])) {
                        $map[] = ['user_id', '=', $get['staff']];
                    } else {
                        $map[] = ['user_id', '=', $user['id']];
                    }
                }
                break;
            case 7: // 洗单组主管
                ## 回放者
                if (!empty($get['staff']) && $get['staff'] == 'all') {
                    $map[] = self::getUserStaffs($user);
                } else if (!empty($get['staff'])) {
                    $map[] = ['user_id', '=', $get['staff']];
                } else {
                    $map[] = ['user_id', '=', $user['id']];
                }
                break;
            case 2: // 洗单组客服
                $map[] = ['user_id', '=', $user['id']];
                break;
            case 3: // 推荐组主管
                ## 回放者
                if (!empty($get['staff']) && $get['staff'] == 'all') {
                    $map[] = self::getUserStaffs($user);
                } else if (!empty($get['staff'])) {
                    $map[] = ['user_id', '=', $get['staff']];
                } else {
                    $map[] = ['user_id', '=', $user['id']];
                }
                break;
            case 4: // 推荐组客服
                if (!isset($get['staff']) || empty($get['staff']))  $map[] = ['user_id', '=', $user['id']];
                break;
            case 10: // 派单组主管
                ## 回放者
                if (!empty($get['staff']) && $get['staff'] == 'all') {
                    $map[] = self::getUserStaffs($user);
                } else if (!empty($get['staff'])) {
                    $map[] = ['user_id', '=', $get['staff']];
                } else {
                    $map[] = ['user_id', '=', $user['id']];
                }
                break;
            case 11: // 派单组客服
                $map[] = ['user_id', '=', $user['id']];
                break;
            case 5: // 门店主管
                if (!empty($get['staff']) && $get['staff'] == 'all') {
                    $map[] = self::getUserStaffs($user);
                } else if (!empty($get['staff'])) {
                    $map[] = ['user_id', '=', $get['staff']];
                } else {
                    $map[] = ['user_id', '=', $user['id']];
                }
                break;
            case 6:
                ## 回放者
                if (!empty($get['staff']) && $get['staff'] == 'all') {
                    $map[] = self::getUserStaffs($user);
                } else if (!empty($get['staff'])) {
                    $map[] = ['user_id', '=', $get['staff']];
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

            default:
                $map[] = ['user_id', '=', $user['id']];
                break;
        }

        if (isset($get['create_time']) && !empty($get['create_time'])) {
            $range = self::getDateRange($get['create_time']);
            $map[] = ['create_time', 'between', $range];
        }

        if (isset($get['next_visit_time']) && !empty($get['next_visit_time'])) {
            $range = self::getDateRange($get['next_visit_time']);
            $map[] = ['next_visit_time', 'between', $range];
        }

        return $map;
    }

    public static function order(&$user, &$get)
    {
        if (isset($get['company_id'])) {
            $map[] = ['company_id', '=', $get['company_id']];
        }

        if (isset($get['source']) && !empty($get['source'])) {
            $map[] = ['source_id', '=', $get['source']];
        }

        if (isset($get['hotel_id']) && !empty($get['hotel_id'])) {
            $map[] = ['hotel_id', '=', $get['hotel_id']];
        }

        if (isset($get['mobile']) && !empty($get['mobile_type'])) {
            $map[] = [$get['mobile_type'], '=' . $get['staff']];
        }

        if (isset($get['staff']) && !empty($get['staff'])) {
            $map[] = ['salesman', '=',  $get['staff']];
        }

        if (isset($get['date_range']) && !empty($get['date_range']) && !empty($get['date_range_type'])) {
            $range = self::getDateRange($get['date_range']);
            $map[] = [$get['date_range_type'], 'between', $range];
        }

        switch ($user['role_id']) {
            case 1: // 管理员
            case 15: // 客服经理
            case 51: // 积分审核
            case 29: // 财务
            case 33: // 出纳
            case 34: // 会计
                break;
            case 7: // 洗单组主管
                $map[] = self::getUserStaffs($user);
                break;
            case 2: // 洗单组客服
                $map[] = ['salesman', '=', $user['id']];
                break;
            case 3: // 推荐组主管
                if (!isset($get['staff'])) $map[] = self::getUserStaffs($user);
                break;
            case 4: // 推荐组客服
                $map[] = ['salesman', '=', $user['id']];
                break;
            case 10: // 派单组主管
                break;
            case 11: // 派单组客服
                $map[] = ['salesman', '=', $user['id']];
                break;
            case 5: // 门店主管
                if (!isset($get['staff'])) $map[] = self::getUserStaffs($user);
                break;

            case 8: // 门店销售
                $map[] = ['salesman', '=', $user['id']];
                break;

            case 9: // 商务会员
                $map[] = ['salesman', '=', $user['id']];
                break;
            case 16: // 拍单组
                break;
            default:
                $map[] = ['salesman', '=', $user['id']];
                break;
        }

        return $map;
    }

    public static function getUserStaffs($user)
    {
        if (!empty($user['department_id'])) {
            $staffs = User::getUsersByDepartmentId($user['department_id']);
            if ($staffs) {
                $map = ['user_id', 'in', $staffs];
            } else {
                $map = ['user_id', '=', $user['id']];
            }
        } else {
            $map = ['user_id', '=', $user['id']];
        }

        return $map;
    }

    public static function getDateRange($dateRange)
    {
        if ($dateRange == 'today') {

            $start = strtotime(date('Y-m-d'));
            $end = strtotime('tomorrow');
        } else {

            $range = explode('~', $dateRange);
            $range[0] = str_replace("+", "", trim($range[0]));
            $range[1] = str_replace("+", "", trim($range[1]));
            $start = strtotime($range[0]);
            $end = strtotime($range[1]) + 86400;
        }

        return [$start, $end];
    }
}
