<?php
namespace app\index\controller\organization;

use app\index\controller\Backend;

class Audit extends Backend
{
    protected function initialize()
    {
        parent::initialize();
        $this->model = ' ';
    }

    // 规则列表
    public function index()
    {
        if($this->request->isAjax()) {

            $result = [
                'code'  => 0,
                'msg'   => '获取数据成功',
                'count' => $list->total(),
                'data'  => $data
            ];
            return json($result);

        } else {

            return $this->fetch();
        }
    }


}