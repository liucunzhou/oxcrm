<?php
namespace app\index\model;

class Search
{
    public static function customerIndex(&$user, &$get)
    {
        switch ($user['role_id']) {
            case 1: // 管理员
            case 15: // 客服经理
                $map[] = ['user_id', '<>', 0];
                // 分配到洗单客服状态过滤
                if (isset($get['assign_status'])) {
                    $map[] = ['assign_status', '=', $get['assign_status']];
                }

                // 有效客资分配到推荐组主管状态
                if (isset($get['active_assign_status'])) {
                    $map[] = ['active_assign_status', '=', $get['active_assign_status']];
                    $map[] = ['wash_status', '=', 1];
                }

                // 洗单客服回访状态过滤
                if (isset($get['wash_status'])) {
                    $map[] = ['wash_status', '=', $get['wash_status']];
                }

                $model = model("MemberAllocate");
                break;
            case 7: // 洗单组主管
                $map[] = ['user_id', '=', $user['id']];
                // 分配到洗单客服状态过滤
                if (isset($get['assign_status'])) {
                    $map[] = ['assign_status', '=', $get['assign_status']];
                }

                // 有效客资分配到推荐组主管状态
                if (isset($get['active_assign_status'])) {
                    $map[] = ['active_assign_status', '=', $get['active_assign_status']];
                    $map[] = ['wash_status', '=', 1];
                }

                // 洗单客服回访状态过滤
                if (isset($get['wash_status'])) {
                    $map[] = ['wash_status', '=', $get['wash_status']];
                }
                $model = model("MemberAllocate");
                break;

            case 2: // 洗单组客服
                $map[] = ['user_id', '=', $user['id']];
                // 分配到洗单客服状态过滤
                if (isset($get['assign_status'])) {
                    $map[] = ['assign_status', '=', $get['assign_status']];
                }

                // 有效客资分配到推荐组主管状态
                if (isset($get['active_assign_status'])) {
                    $map[] = ['active_assign_status', '=', $get['active_assign_status']];
                    $map[] = ['wash_status', '=', 1];
                }

                // 洗单客服回访状态过滤
                if (isset($get['wash_status'])) {
                    $map[] = ['wash_status', '=', $get['wash_status']];
                }
                $model = model("MemberAllocate");
                break;

            case 3: // 推荐组主管
            case 4: // 推荐组客服
                $map[] = ['user_id', '=', $user['id']];
                if (isset($get['assign_status'])) {
                    $map[] = ['assign_status', '=', $get['assign_status']];
                }
                $model = model('RecommendAllocate');
                break;

            case 10: // 派单组主管
            case 11: // 派单组客服
                $map[] = ['user_id', '=', $user['id']];
                if (isset($get['assign_status'])) {
                    $map[] = ['assign_status', '=', $get['assign_status']];
                }
                $model = model('DispatchAllocate');
                break;
            default :
                $model = [];
                $map = [];
        }

        return ['model' => $model, 'map' => $map];
    }

    public static function customerMine(&$user, &$get)
    {
        if (isset($get['order_status'])) {
            $map[] = ['order_status', '=', $get['order_status']];
        }

        switch ($user['role_id']) {
            case 1: // 管理员
            case 15: // 客服经理
                if (isset($get['status'])) {
                    $map[] = ['intention_status', '=', $get['status']];
                }
                $Model = model('MemberAllocate');
                break;
            case 7: // 洗单组主管
                $map[] = ['user_id', '=', $user['id']];
                // 查询回访状态
                $map[] = ['user_id', '=', $user['id']];
                if (isset($get['status'])) {
                    $map[] = ['intention_status', '=', $get['status']];
                }
                $Model = model('MemberAllocate');
                break;
            case 2: // 洗单组客服
                $map[] = ['user_id', '=', $user['id']];
                if (isset($get['status'])) {
                    $map[] = ['intention_status', '=', $get['status']];
                }
                $Model = model('MemberAllocate');
                break;
            case 3: // 推荐组主管
                $map[] = ['user_id', '=', $user['id']];
                if (isset($get['status'])) {
                    $map[] = ['intention_status', '=', $get['status']];
                }
                $Model = model('RecommendAllocate');
                break;
            case 4: // 推荐组客服
                $map[] = ['user_id', '=', $user['id']];
                if (isset($get['status'])) {
                    $map[] = ['intention_status', '=', $get['status']];
                }
                $Model = model('RecommendAllocate');
                break;
            case 10: // 派单组主管
                $map[] = ['user_id', '=', $user['id']];
                if (isset($get['status'])) {
                    $map[] = ['intention_status', '=', $get['status']];
                }
                $Model = model('DispatchAllocate');
                break;
            case 11: // 派单组客服
                $map[] = ['user_id', '=', $user['id']];
                if (isset($get['status'])) {
                    $map[] = ['intention_status', '=', $get['status']];
                }
                $Model = model('DispatchAllocate');
                break;
            case 15: // 客服部经理
                $map[] = ['user_id', '<>', '0'];
                if (isset($get['status'])) {
                    $map[] = ['intention_status', '=', $get['status']];
                }
                $Model = model('StoreAllocate');
                break;

            case 5: // 门店主管
                $map[] = ['user_id', '=', $user['id']];
                if (isset($get['status'])) {
                    $map[] = ['intention_status', '=', $get['status']];
                }
                $Model = model('StoreAllocate');
                break;

            case 8: // 门店客服
                $map[] = ['user_id', '=', $user['id']];
                if (isset($get['status'])) {
                    $map[] = ['intention_status', '=', $get['status']];
                }
                $Model = model('StoreAllocate');
                break;

            case 9: // 门店客服
                $map[] = ['user_id', '=', $user['id']];
                if (isset($get['status'])) {
                    $map[] = ['intention_status', '=', $get['status']];
                }
                $Model = model('MerchantAllocate');
                break;

            default :
                $map = [];
                $Model = [];
                break;
        }

        return ['model' => $Model, 'map' => $map];
    }
}