<?php
namespace app\h5\controller\order;

use app\common\model\OrderConfirm;
use app\common\model\OrderConfirmComment;
use app\h5\controller\Base;

class Comment extends Base
{
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * token
     * confirm_id,
     * content
     * image
     */
    public function create()
    {
        $param = $this->request->param();
        $confirm = OrderConfirm::get($param['id']);
        $data = [];
        $data['confirm_id'] = $param['id'];
        $data['order_id'] = $confirm->order_id;
        $data['confirm_no'] = $confirm->confirm_no;
        $data['operate_id'] = $this->user['id'];
        $data['user_id'] = $this->user['id'];
        $data['content'] = $param['content'];
        $data['image'] = $param['image'];

        $model = new OrderConfirmComment();
        $result = $model->allowField(true)->insert($data);
        if($result) {
            $arr = [
                'code'  => '200',
                'msg' => '评论成功',
            ];
        } else {
            $arr = [
                'code'  => '400',
                'msg' => '评论失败',
            ];
        }

        return json($arr);
    }
}