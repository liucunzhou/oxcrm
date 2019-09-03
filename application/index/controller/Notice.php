<?php
namespace app\index\controller;

use think\facade\Request;

class Notice extends Base
{
    public function index()
    {
        if (Request::isAjax()) {


        } else {

            return $this->fetch();
        }

    }

    public function getNotice()
    {
        $NoticeModel = new \app\common\model\Notice();
        $notice = $NoticeModel->getNoticeFromQueue($this->user['id']);

        if (!empty($notice)) {
            return json([
                'code' => '200',
                'msg' => '获取消息成功',
                'result' => $notice
            ]);
        } else {
            return json([
                'code' => '500',
                'msg' => '暂无消息'
            ]);
        }
    }
}