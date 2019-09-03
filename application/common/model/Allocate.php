<?php
namespace app\common\model;

class Allocate
{
    public static function updateAllocate($userId, $memberId, &$data)
    {
        $auth = UserAuth::getUserLogicAuth($userId);
        $AllocateModel = null;
        switch ($auth['role_ids']) {
            case 3: // 推荐组主管
            case 4: // 推荐组客服
                $AllocateModel = new MemberAllocate();
                break;
            case 10: // 派单组主管
            case 11: // 派单组客服
                $AllocateModel = new DispatchAllocate();
                break;
            case 5: // 门店店长
            case 8: // 门店销售
                $AllocateModel = new StoreAllocate();
                break;
            case 9: // 商户会员
                $AllocateModel = new MerchantAllocate();
                break;
            default:
                $AllocateModel = new MemberAllocate();

        }

        $map[] = ['user_id', '=', $userId];
        $map[] = ['member_id', '=', $memberId];
        $origin = $AllocateModel->where($map)->find();
        if (empty($origin)) {
            $data['user_id'] = $userId;
            $data['member_id'] = $memberId;
            $AllocateModel->allowField(true)->save($data);
        } else {
            $AllocateModel->allowField(true)->save($data, $map);
        }
        return $AllocateModel;
    }

    public static function updateAllocateByModel(&$model, &$user, &$data)
    {

        // $data['active_status'] = (int)$data['active_status'];
        $result = $model->allowField(true)->save($data);
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
                $map[] = ['user_id', '=', $user['id']];
                $AllocateModel = model("MemberAllocate");

        }
        $map[] = ['member_id', '=', $memberId];
        $AllocateModel = $AllocateModel->where($map)->order('id desc')->find();
        return $AllocateModel;
    }

    public static function assignToWashGroup($operateId, $userId, &$Member)
    {
        $map = [];
        $map[] = ['member_id', '=', $Member->id];
        $map[] = ['user_id', '=', $userId];
        $Allocate = new MemberAllocate();
        ### 检测是否分配过
        $allocated= $Allocate->where($map)->find();
        if(!empty($allocated)) return false;

        $data = self::setAllocateData($operateId, $userId, $Member);
        $result = $Allocate->save($data);

        return $result;
    }

    public static function assignToRecommendGroup($operateId, $userId, &$Member)
    {
        $map = [];
        $map[] = ['member_id', '=', $Member->id];
        $map[] = ['user_id', '=', $userId];
        $Allocate = new RecommendAllocate();
        ### 检测是否分配过
        $allocated= $Allocate->where($map)->find();
        if(!empty($allocated)) return false;

        $data = self::setAllocateData($operateId, $userId, $Member);
        $result = $Allocate->save($data);

        return $result;
    }

    public static function assignToStoreGroup($operateId, $userId, &$Member)
    {
        $map = [];
        $map[] = ['member_id', '=', $Member->id];
        $map[] = ['user_id', '=', $userId];
        $Allocate = new StoreAllocate();
        ### 检测是否分配过
        $allocated= $Allocate->where($map)->find();
        if(!empty($allocated)) return false;

        $data = self::setAllocateData($operateId, $userId, $Member);
        $result = $Allocate->save($data);

        return $result;
    }

    ### 分配客资基本信息到用户
    ### 基本信息包括：客户编号、信息类型、来源、推荐人、回访状态、预算、桌数、酒店、酒店名称、区域、城市
    public static function setAllocateData($operateId, $userId, &$Member)
    {
        $data = [];
        $data['operate_id'] = $operateId;
        $data['user_id'] = $userId;
        $data['member_id'] = $Member->id;
        $data['news_type'] = $Member->news_type;
        $data['source_id'] = $Member->source_id;
        $data['recommender_id'] = $Member->recommender_id;
        $data['source_text'] = $Member->source_text;
        $data['active_status'] = 0;
        $data['active_assign_status'] = 0;
        $data['budget'] = $Member->budget;
        $data['banquet_size'] = $Member->banquet_size;
        $data['hotel_id'] = $Member->hotel_id;
        $data['hotel_text'] = $Member->hotel_text;
        $data['province_id'] = $Member->province_id;
        $data['city_id'] = $Member->city_id;
        $data['area_id'] = $Member->area_id;
        $data['zone'] = $Member->zone;
        $data['create_time'] = time();

        return $data;
    }
}