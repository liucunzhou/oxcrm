<?php
namespace app\index\model;

class Tab
{
    public static function customerIndex(&$user, &$get)
    {
        switch ($user['role_id']) {
            case 1: //
            case 15: // 客服经理
            case 7: // 洗单组主管
                $tabs = [
                    ['text' => '未分配', 'url' => url('customer/index', ['assign_status' => 0]), 'checked' => isset($get['assign_status']) && $get['assign_status'] === '0' ? 1 : 0],
                    ['text' => '已分配', 'url' => url('customer/index', ['assign_status' => 1]), 'checked' => isset($get['assign_status']) && $get['assign_status'] === '1' ? 1 : 0],
                    ['text' => '有效已分配', 'url' => url('customer/index', ['active_assign_status' => 1]), 'checked' => isset($get['active_assign_status']) && $get['active_assign_status'] === '1' ? 1 : 0],
                    ['text' => '有效未分配', 'url' => url('customer/index', ['active_assign_status' => 0]), 'checked' => empty($get) || $get['active_assign_status'] === '0' ? 1 : 0],
                    ['text' => '无效客资', 'url' => url('customer/index', ['wash_status' => 2]), 'checked' => isset($wash_satatus) && $get['wash_status'] === '2' ? 1 : 0],
                    ['text' => '跟进中', 'url' => url('customer/index', ['wash_status' => 3]), 'checked' => isset($wash_satatus) && $get['wash_status'] === '3' ? 1 : 0],
                    ['text' => '未跟进', 'url' => url('customer/index', ['wash_status' => 0]), 'checked' => isset($wash_satatus) && $get['wash_status'] === '0' ? 1 : 0],
                ];
                break;
            case 2: // 洗单组客服
                $tabs = [
                    ['text' => '有效已分配', 'url' => url('customer/index', ['active_assign_status' => 1]), 'checked' => isset($get['active_assign_status']) && $get['active_assign_status'] === '1' ? 1 : 0],
                    ['text' => '有效未分配', 'url' => url('customer/index', ['active_assign_status' => 0]), 'checked' => empty($get)|| $get['active_assign_status'] === '0' ? 1 : 0],
                    ['text' => '无效客资', 'url' => url('customer/index', ['wash_status' => 2]), 'checked' => isset($wash_satatus) && $get['wash_status'] === '2' ? 1 : 0],
                    ['text' => '跟进中', 'url' => url('customer/index', ['wash_status' => 3]), 'checked' => isset($wash_satatus) && $get['wash_status'] === '3' ? 1 : 0],
                    ['text' => '未跟进', 'url' => url('customer/index', ['wash_status' => 0]), 'checked' => isset($wash_satatus) && $get['wash_status'] === '0' ? 1 : 0],
                ];
                break;
            case 3: // 推荐组主管
            case 4: // 推荐组客服
            case 10: // 派单组主管
            case 11: // 派单组客服
                $tabs = [
                    ['text' => '已分配', 'url' => url('customer/index', ['assign_status' => 1]), 'checked' => isset($get['assign_status']) && $get['assign_status'] === '1' ? 1 : 0],
                    ['text' => '未分配', 'url' => url('customer/index', ['assign_status' => 0]), 'checked' => empty($get) || $get['assign_status'] === '0' ? 1 : 0],
                ];
                break;
            case 5:// 门店店长
            case 8:// 门店销售
                break;
            default :
                $tabs = [];
        }

        return $tabs;
    }

    public static function customerMine(&$user, &$get)
    {
        // 生成导航
        switch ($user['role_id']) {
            case 7:
            case 2:
                $tabs = [
                    ['text' => '所有客资', 'url' => url('customer/mine'), 'checked' => !isset($get['status']) && !isset($get['order_status']) ? 1 : 0],
                    ['text' => '待跟进', 'url' => url('customer/mine', ['status' => 0]), 'checked' => isset($get['status']) && $get['status'] === '0' ? 1 : 0],
                    ['text' => '有效客资', 'url' => url('customer/mine', ['status' => 1]), 'checked' => isset($get['status']) && $get['status'] === '1' ? 1 : 0],
                    ['text' => '无效客资', 'url' => url('customer/mine', ['status' => 3]), 'checked' => isset($get['status']) && $get['status'] === '3' ? 1 : 0],
                    ['text' => '成单客资', 'url' => url('customer/mine', ['order_status' => 1]), 'checked' => isset($get['order_status']) && $get['order_status'] === '1' ? 1 : 0],
                ];
                break;
            default :
                $tabs = [
                    ['text' => '所有客资', 'url' => url('customer/mine'), 'checked' => !isset($get['status']) && !isset($get['order_status']) ? 1 : 0],
                    ['text' => '未跟进', 'url' => url('customer/mine', ['status' => 0]), 'checked' => isset($get['status']) && $get['status'] === '0' ? 1 : 0],
                    ['text' => '跟进中', 'url' => url('customer/mine', ['status' => 2]), 'checked' => isset($get['status']) && $get['status'] === '2' ? 1 : 0],
                    ['text' => '有效客资', 'url' => url('customer/mine', ['status' => 1]), 'checked' => isset($get['status']) && $get['status'] === '1' ? 1 : 0],
                    ['text' => '无效客资', 'url' => url('customer/mine', ['status' => 3]), 'checked' => isset($get['status']) && $get['status'] === '3' ? 1 : 0],
                    ['text' => '失效客资', 'url' => url('customer/mine', ['status' => 4]), 'checked' => isset($get['status']) && $get['status'] === '4' ? 1 : 0],
                    ['text' => '成单客资', 'url' => url('customer/mine', ['order_status' => 1]), 'checked' => isset($get['order_status']) && $get['order_status'] === '1' ? 1 : 0],
                ];
        }

        return $tabs;
    }

    public static function customerApply($get)
    {
        $tabs = [
            ['text' => '未审核', 'url' => url('customer/apply', ['status' => 0]), 'checked' => !isset($get['status']) || ( isset($get['status']) && $get['status'] === '0') ? 1 : 0],
            ['text' => '已通过', 'url' => url('customer/apply', ['status' => 1]), 'checked' => isset($get['status']) && $get['status'] === '1' ? 1 : 0],
            ['text' => '未通过', 'url' => url('customer/apply', ['status' => 2]), 'checked' => isset($get['status']) && $get['status'] === '2' ? 1 : 0],
        ];

        return $tabs;
    }

    public static function managerApply($get)
    {
        $tabs = [
            ['text' => '未审核', 'url' => url('manager/applyVisit', ['status' => 0]), 'checked' => !isset($get['status']) || ( isset($get['status']) && $get['status'] === '0') ? 1 : 0],
            ['text' => '已通过', 'url' => url('manager/applyVisit', ['status' => 1]), 'checked' => isset($get['status']) && $get['status'] === '1' ? 1 : 0],
            ['text' => '未通过', 'url' => url('manager/applyVisit', ['status' => 2]), 'checked' => isset($get['status']) && $get['status'] === '2' ? 1 : 0],
        ];

        return $tabs;
    }
}