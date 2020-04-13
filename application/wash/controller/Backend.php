<?php

namespace app\wash\controller;

use think\Controller;
use think\Request;

class Backend extends Controller
{
    protected $model;
    protected $user = [];
    protected $middleware = ['Auth'];
    protected $newsTypes = ['婚宴信息', '婚庆信息', '一站式', '婚纱摄影', '婚车', '婚纱礼服', '男装', '宝宝宴', '会务'];
    protected $allocateTypes = ['分配获取', '全号搜索', '公海申请', '自行添加'];
    protected $levels = [
        999 => [
            'title' => '非常重要',
            'btn' => 'btn-danger'
        ],
        998 => [
            'title' => '重要',
            'btn' => 'btn-warning'
        ],
        997 => [
            'title' => '一般',
            'btn' => 'btn-primary'
        ],
        0 => [
            'title' => '无',
            'btn' => 'btn-info'
        ]
    ];

    protected function initialize()
    {
        $user = session('user');
        $this->user = $user;
        $this->assign('user', $user);
        $this->assign('newsTypes', $this->newsTypes);
        $this->assign('allocateTypes', $this->allocateTypes);

        $this->assign('levels', $this->levels);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    public function _empty()
    {
        return '持续更新中...';
    }
}
