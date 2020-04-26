<?php
namespace app\h5\controller\dictionary;


use app\h5\controller\Base;

class Sugar extends Base
{
    protected $model = null;
    protected function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\Sugar();
    }

    public function getList()
    {
        $list = $this->model->field('id,title,price')->order('sort desc')->select();

        $result = [
            'code'  =>  '200',
            'msg'   =>  '获取信息成功',
            'data'  =>  [
                'sugarList'  => $list
            ]
        ];

        return json($result);
    }
}