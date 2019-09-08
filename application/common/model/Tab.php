<?php
namespace app\common\model;

class Tab
{
    public static function source($get)
    {
        $tabs = [];

        $vars = [];
        $url = url('Source/index', $vars);
        if (!isset($get['parent_id'])) {
            $checked = 1;
        } else {
            $checked = 0;
        }
        $tabs[] = [
            'text' => '来源',
            'url' => $url,
            'checked' => $checked
        ];

        if (isset($get['parent_id'])) {
            $checked = 1;
        } else {
            $checked = 0;
        }
        $vars = ['parent_id' => 'yes'];
        $url = url('Source/index', $vars);
        $tabs[] = [
            'text' => '渠道',
            'url' => $url,
            'checked' => $checked
        ];

        return $tabs;
    }

    public static function customerMine(&$user, &$get)
    {
        $status = Intention::getIntentions();
        switch ($user['role_id']) {
            case 1: //
            case 15: // 客服经理
            case 7: // 洗单组主管
            case 2: // 洗单组客服
            case 3: // 推荐组主管
            case 4: // 推荐组客服
            case 10: // 派单组主管
            case 11: // 派单组客服
                $tabs = [];
                foreach ($status as $key => $val) {
                    $item = [];
                    $vars = [];
                    if ($val['id'] == 5) { // 有效客资
                        if (isset($get['active_assign_status'])) {
                            $vars['status'] = 5;
                            // 有效已分配选中转台
                            $vars['active_assign_status'] = 1;
                            $url = url('customer/mine', $vars);
                            $checked = $get['active_assign_status'] == 1 ? 1 : 0;
                            $item = [
                                'text' => '有效已分配',
                                'url' => $url,
                                'checked' => $checked
                            ];
                            $tabs[] = $item;

                            // 有效未分配未选状态
                            $vars['active_assign_status'] = 0;
                            $url = url('customer/mine', $vars);
                            $checked = $get['active_assign_status'] == 0 ? 1 : 0;
                            $item = [
                                'text' => '有效未分配',
                                'url' => $url,
                                'checked' => $checked
                            ];
                        } else {
                            // 有效已分配未选状态
                            $vars['status'] = 5;
                            $vars['active_assign_status'] = 1;
                            $url = url('customer/mine', $vars);
                            $checked = 0;
                            $item = [
                                'text' => '有效已分配',
                                'url' => $url,
                                'checked' => $checked
                            ];
                            $tabs[] = $item;

                            // 有效未分配未选状态
                            $vars['active_assign_status'] = 0;
                            $url = url('customer/mine', $vars);
                            $checked = 0;
                            $item = [
                                'text' => '有效未分配',
                                'url' => $url,
                                'checked' => $checked
                            ];
                        }
                    } else if ($val['id'] == 6) { // 意向客资
                        if (isset($get['possible_assign_status'])) {
                            $vars['status'] = 6;
                            // 有效已分配选中转台
                            $vars['possible_assign_status'] = 1;
                            $url = url('customer/mine', $vars);
                            $checked = $get['possible_assign_status'] == 1 ? 1 : 0;
                            $item = [
                                'text' => '意向已分配',
                                'url' => $url,
                                'checked' => $checked
                            ];
                            $tabs[] = $item;

                            // 有效未分配未选状态
                            $vars['possible_assign_status'] = 0;
                            $url = url('customer/mine', $vars);
                            $checked = $get['possible_assign_status'] == 0 ? 1 : 0;
                            $item = [
                                'text' => '意向未分配',
                                'url' => $url,
                                'checked' => $checked
                            ];
                        } else {
                            $vars['status'] = 6;
                            // 有效已分配未选状态
                            $vars['possible_assign_status'] = 1;
                            $url = url('customer/mine', $vars);
                            $checked = 0;
                            $item = [
                                'text' => '意向已分配',
                                'url' => $url,
                                'checked' => $checked
                            ];
                            $tabs[] = $item;

                            // 有效未分配未选状态
                            $vars['possible_assign_status'] = 0;
                            $url = url('customer/mine', $vars);
                            $checked = 0;
                            $item = [
                                'text' => '意向未分配',
                                'url' => $url,
                                'checked' => $checked
                            ];
                        }
                    } else {
                        $vars['status'] = $val['id'];
                        $url = url('customer/mine', $vars);
                        $checked = isset($get['status']) && $get['status'] == $val['id'] ? 1 : 0;
                        $item = [
                            'text' => $val['title'],
                            'url' => $url,
                            'checked' => $checked
                        ];
                    }
                    $tabs[] = $item;
                }
                break;
            case 5:// 门店店长
            case 8:// 门店销售
            default :
                $tabs = [];
                foreach ($status as $key => $val) {
                    $vars['status'] = $val['id'];
                    $url = url('customer/mine', $vars);

                    $checked = isset($get['status']) && $get['status'] == $val['id'] ? 1 : 0;
                    $item = [
                        'text' => $val['title'],
                        'url' => $url,
                        'checked' => $checked
                    ];
                    $tabs[] = $item;
                }
        }
        $vars = [];
        $vars['is_into_store'] = 1;
        $url = url('customer/mine', $vars);
        if (isset($get['is_into_store']) && $get['is_into_store'] == 1) {
            $checked = 1;
        } else {
            $checked = 0;
        }
        $tabs[] = [
            'text' => '已进店',
            'url' => $url,
            'checked' => $checked
        ];

        $vars = [];
        $url = url('customer/mine');
        if (!isset($get['status']) && !isset($get['is_into_store'])) {
            $checked = 1;
        } else {
            $checked = 0;
        }

        $all = [
            'text' => '所有客资',
            'url' => $url,
            'checked' => $checked
        ];
        array_unshift($tabs, $all);

        return $tabs;
    }

    public static function customerToday(&$user, &$get)
    {
        $vars = [];
        $vars['status'] = 0;
        $url = url('customer/today', $vars);
        if (!isset($get['status']) || $get['status'] == 0) {
            $checked = 1;
        } else {
            $checked = 0;
        }
        $tabs[] = [
            'text' => '未跟进',
            'url' => $url,
            'checked' => $checked
        ];

        $vars = [];
        $url = url('customer/today');
        if (!isset($get['status']) && !isset($get['is_into_store'])) {
            $checked = 1;
        } else {
            $checked = 0;
        }

        $all = [
            'text' => '已跟进',
            'url' => $url,
            'checked' => $checked
        ];
        array_unshift($tabs, $all);

        return $tabs;
    }

    public static function customerApply($get)
    {
        $tabs = [
            ['text' => '未审核', 'url' => url('customer/apply', ['status' => 0]), 'checked' => !isset($get['status']) || (isset($get['status']) && $get['status'] === '0') ? 1 : 0],
            ['text' => '已通过', 'url' => url('customer/apply', ['status' => 1]), 'checked' => isset($get['status']) && $get['status'] === '1' ? 1 : 0],
            ['text' => '未通过', 'url' => url('customer/apply', ['status' => 2]), 'checked' => isset($get['status']) && $get['status'] === '2' ? 1 : 0],
        ];

        return $tabs;
    }

    public static function orderApply($get)
    {
        $tabs = [
            ['text' => '未审核', 'url' => url('order/orderApply', ['status' => 0]), 'checked' => !isset($get['status']) || (isset($get['status']) && $get['status'] === '0') ? 1 : 0],
            ['text' => '已通过', 'url' => url('order/orderApply', ['status' => 1]), 'checked' => isset($get['status']) && $get['status'] === '1' ? 1 : 0],
            ['text' => '未通过', 'url' => url('order/orderApply', ['status' => 2]), 'checked' => isset($get['status']) && $get['status'] === '2' ? 1 : 0],
        ];

        return $tabs;
    }

    public static function managerApply(&$get)
    {
        $tabs = [
            ['text' => '未审核', 'url' => url('manager/applyVisit', ['status' => 0]), 'checked' => !isset($get['status']) || (isset($get['status']) && $get['status'] === '0') ? 1 : 0],
            ['text' => '已通过', 'url' => url('manager/applyVisit', ['status' => 1]), 'checked' => isset($get['status']) && $get['status'] === '1' ? 1 : 0],
            ['text' => '未通过', 'url' => url('manager/applyVisit', ['status' => 2]), 'checked' => isset($get['status']) && $get['status'] === '2' ? 1 : 0],
        ];
        return $tabs;
    }

    public static function applyToOrder(&$get)
    {
        $tabs = [
            ['text' => '未审核', 'url' => url('manager/applyToOrder', ['status' => 0]), 'checked' => !isset($get['status']) || (isset($get['status']) && $get['status'] === '0') ? 1 : 0],
            ['text' => '已通过', 'url' => url('manager/applyToOrder', ['status' => 1]), 'checked' => isset($get['status']) && $get['status'] === '1' ? 1 : 0],
            ['text' => '未通过', 'url' => url('manager/applyToOrder', ['status' => 2]), 'checked' => isset($get['status']) && $get['status'] === '2' ? 1 : 0],
        ];
        return $tabs;
    }
}