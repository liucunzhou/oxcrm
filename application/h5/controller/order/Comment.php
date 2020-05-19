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
        $model = new OrderConfirmComment();
        
    }
}