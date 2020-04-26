<?php
namespace app\h5\controller\dictionary;


use app\h5\controller\Base;

class D3 extends Base
{
    protected $model = null;
    protected function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\D3();
    }

    public function getList()
    {
        $list = $this->model->field('id,title,price')->order('sort desc')->select();

        $result = [
            'code'  =>  '200',
            'msg'   =>  '获取信息成功',
            'data'  =>  [
                '3dList'  => $list
            ]
        ];

        return json($result);
    }
}