<?php

namespace app\admin\controller\store;

use app\admin\controller\Backend;
use think\Request;

class Hall extends Backend
{

    protected function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\BanquetHall();
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $params = $this->request->param();
    
        $where = [];
        if(isset($params['store_id'])) {
            $where['store_id'] = $params['store_id'];
        }
        $list = $this->model->where($where)->paginate(15);
        $this->assign('list', $list);

        return $this->fetch();
    }
}
