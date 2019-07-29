<?php
namespace app\index\model;

class Allocate
{
    public static function updateAllocate($userId, $memberId, &$data)
    {
        $auth = UserAuth::getUserLogicAuth($userId);
        $AllocateModel = null;
        switch ($auth['role_ids']) {
            case 3: // 推荐组主管
            case 4: // 推荐组客服
                $AllocateModel = model("RecommendAllocate");
                break;
            case 10: // 派单组主管
            case 11: // 派单组客服
                $AllocateModel = model("DispatchAllocate");
                break;
            case 5: // 门店店长
            case 8: // 门店销售
                $AllocateModel = model("StoreAllocate");
                break;
            case 9: // 商户会员
                $AllocateModel = model("MerchantAllocate");
                break;
            default:
                $AllocateModel = model("MemberAllocate");

        }
        $map[] = ['user_id', '=', $userId];
        $map[] = ['member_id', '=', $memberId];
        $origin = $AllocateModel->where($map)->find();
        if (empty($origin)) {
            $AllocateModel->insert($data);
        } else {
            $AllocateModel->save($data, $map);
        }
        return $AllocateModel;
    }

    public static function updateAllocateByModel(&$model, &$user, &$data)
    {
        if(in_array($user['role_id'], [2,7])) { // 洗单组
            $data['wash_status'] = (int)$data['status'];
        } else {
            $data['intention_status'] = (int)$data['status'];
        }

        $result = $model->save($data);
        return $result;
    }

    public static function getAllocate($user, $memberId)
    {
        $AllocateModel = null;
        switch ($user['role_id']) {
            case 3: // 推荐组主管
            case 4: // 推荐组客服
                $map[] = ['user_id', '=', $user['id']];
                $AllocateModel = model("RecommendAllocate");
                break;
            case 10: // 派单组主管
            case 11: // 派单组客服
                $map[] = ['user_id', '=', $user['id']];
                $AllocateModel = model("DispatchAllocate");
                break;
            case 5: // 门店店长
            case 8: // 门店销售
                $map[] = ['user_id', '=', $user['id']];
                $AllocateModel = model("StoreAllocate");
                break;
            case 9: // 商户会员
                $map[] = ['user_id', '=', $user['id']];
                $AllocateModel = model("MerchantAllocate");
                break;
            default:
                $AllocateModel = model("MemberAllocate");

        }
        $map[] = ['member_id', '=', $memberId];
        $AllocateModel = $AllocateModel->where($map)->find();

        return $AllocateModel;
    }
}