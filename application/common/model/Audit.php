<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Audit extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function getList()
    {
        $data = self::order('is_valid desc,sort desc,id asc')->column('id,title,is_valid', 'id');
        return $data;
    }

    public static function createOrderConfirm($companyId, $orderId, $userId, $current=0) {
        $audit = \app\common\model\Audit::where('company_id', '=', $companyId)->find();

        $config = config();
        // 审核流程
        $sequence = json_decode($audit->content, true);
        if(empty($current)) {
            $first = key($sequence);
        } else {

        }

        $auditConfig = $config['crm']['check_sequence'];
        if($auditConfig[$first]['type'] == 'staff') {
            // 指定人员审核
            foreach ($sequence[$first] as $row)
            {
                $data = [];
                $data['company_id'] = $companyId;
                $data['confirm_item_id'] = $first;
                $data['confirm_user_id'] = $row;
                $data['user_id'] = $userId;
                $data['order_id'] = $orderId;
                $data['status'] = 0;
                $orderConfirm = new OrderConfirm();
                $orderConfirm->allowField(true)->save($data);
            }
        } else {
            $user = User::getUser($userId);
            // 指定角色审核
            foreach ($sequence[$first] as $row) {
                $staff = User::getRoleManager($row, $user);
                $data = [];
                $data['company_id'] = $companyId;
                $data['confirm_item_id'] = $current;
                $data['confirm_user_id'] = $staff->id;
                $data['user_id'] = $userId;
                $data['order_id'] = $orderId;
                $data['status'] = 0;
                $orderConfirm = new OrderConfirm();
                $orderConfirm->allowField(true)->save($data);
            }
        }
    }
}