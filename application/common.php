<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
### 清除两端的空格
if (!function_exists('clear_both_blank')) {
    function clear_both_blank($data)
    {
        $data = trim($data);
        $data = preg_replace("/\s(?=\s)/", "", $data);
        $data = preg_replace("/^[(\xc2\xa0)|\s]+/", "", $data);
        $data = preg_replace("/[\n\r\t]/", ' ', $data);
        return $data;
    }
}

### csv转码
if(!function_exists('csv_convert_encoding')) {
    function csv_convert_encoding($data)
    {
        $encoding = mb_detect_encoding($data);
        $data = mb_convert_encoding($data, 'UTF-8', ['Unicode', 'ASCII', 'GB2312', 'GBK', 'UTF-8', 'ISO-8859-1']);
        return $data;
    }
}

if(!function_exists('get_news_types')) {
    function get_news_types($newsTypesText)
    {
        if (empty($newsTypesText)) return '';
        $newsTypes = ['婚宴信息', '婚庆信息', '一站式', '婚纱摄影', '婚车', '婚纱礼服', '男装', '宝宝宴', '会务'];
        $arr = explode(',', $newsTypesText);
        $texts = [];
        foreach ($arr as $key=>$val) {
            $texts[] = $newsTypes[$val];
        }
        return implode(',', $texts);
    }
}

### 获取下一个审核顺序
if(!function_exists('get_next_confirm_item')) {
    function get_next_confirm_item($current, $sequence)
    {
        foreach ($sequence as $key => $value) {
            next($sequence);
            if ($key == $current) break;
        }

        return key($sequence);
    }
}

### 创建审核顺序
if(!function_exists('create_order_confirm')) {
    function create_order_confirm($orderId, $companyId, $userId, $confirmType='income')
    {
        ### 审核流程
        $where = [];
        $where[] = ['company_id', '=', $companyId];
        $where[] = ['timing', '=', $confirmType];
        $audit = \app\common\model\Audit::where('company_id', '=', $companyId)->find();
        $sequence = json_decode($audit->content, true);

        ### 获取当前订单，当前用户的审核流程
        $where = [];
        $where[] = ['user_id', '=', $userId];
        $where[] = ['order_id', '=', $orderId];
        $where[] = ['company_id', '=', $companyId];
        $where[] = ['confirm_type', '=', $confirmType];

        $confirmList = \app\common\model\OrderConfirm::where($where)->order('id desc')->select();
        if($confirmList->isEmpty()) {
            ### 该员工、该订单、该承办公司第一次审核
            $index = key($sequence);
        } else {
            $where = [];
            $where[] = ['user_id', '=', $userId];
            $where[] = ['order_id', '=', $orderId];
            $where[] = ['company_id', '=', $companyId];
            $where[] = ['confirm_type', '=', $confirmType];
            $where[] = ['is_checked', '=', 0];
            $confirmLast = \app\common\model\OrderConfirm::where($where)->order('id desc')->find();
            $current = $confirmLast->confirm_item_id;
            $index = get_next_confirm_item($current, $sequence);
            if(is_null($index)) {
                // 已审核完的状态可以添加审核
                $index = key($sequence);
            } else {
                $index = key($sequence);
            }
        }

        $config = config();
        $auditConfig = $config['crm']['check_sequence'];
        if($auditConfig[$index]['type'] == 'staff') {
            // 指定人员审核
            foreach ($sequence[$index] as $row)
            {
                $data = [];
                $data['confirm_no'] = date('YmdHis').mt_rand(10000,99999);
                $data['confirm_type'] = $confirmType;
                $data['company_id'] = $companyId;
                $data['confirm_item_id'] = $index;
                $data['confirm_user_id'] = $row;
                $data['user_id'] = $userId;
                $data['order_id'] = $orderId;
                $data['status'] = 0;
                $data['is_checked'] = 0;
                $orderConfirm = new \app\common\model\OrderConfirm();
                $orderConfirm->allowField(true)->save($data);
            }
        } else {
            $user = \app\common\model\User::getUser($userId);
            // 指定角色审核
            foreach ($sequence[$index] as $row) {
                $staff = \app\common\model\User::getRoleManager($row, $user);
                $data = [];
                $data['confirm_no'] = date('YmdHis').mt_rand(10000,99999);
                $data['confirm_type'] = $confirmType;
                $data['company_id'] = $companyId;
                $data['confirm_item_id'] = $index;
                $data['confirm_user_id'] = $staff->id;
                $data['user_id'] = $userId;
                $data['order_id'] = $orderId;
                $data['status'] = 0;
                $data['is_checked'] = 0;
                $orderConfirm = new \app\common\model\OrderConfirm();
                $orderConfirm->allowField(true)->save($data);
            }
        }

        return 1;
    }
}