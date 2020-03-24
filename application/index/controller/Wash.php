<?php
namespace app\index\controller;

use think\facade\Request;

class Wash extends Base
{
    // 通话统计
    public function call()
    {
        $params = $this->request->param();

        $callLog = new \app\common\model\CallLog();
        $callRecord = new \app\common\model\CallRecord();
        
        $where = [];
        $where[] = ['create_time', 'between', [$start, $end]];
        // $user = $callLog->where($where)->

        return $this->fetch();
    }
}