<?php
namespace app\h5\controller\dictionary;

use app\h5\controller\Base;

class Source extends Base
{
    public $model = null;

    protected function initialize()
    {
        $this->model = new \app\common\model\Source();
    }

    public function index()
    {
        $param = $this->request->param();
        $map = [];
        if( !empty($param['title']) ){
            $map[] = ['title' ,'like','%'.$param['title'].'%'];
            $field = 'id,title';
            $sourceList = $this->model->where($map)->field($field)->order('sort desc')->select();
        } else {
            $sourceList = [];
        }

        $result = [
            'code'  => '200',
            'msg'   => '获取数据成功',
            'data'  => [
                'sourceList' => $sourceList
            ]
        ];

        return json($result);
    }
}