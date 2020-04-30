<?php

namespace app\h5\controller\order;


use app\common\model\OrderBanquet;
use app\common\model\Package;
use app\common\model\Ritual;
use app\h5\controller\Base;

class Banquet extends Base
{
    protected $model = null;

    public function initialize()
    {
        parent::initialize();
        $this->model = new OrderBanquet();
    }

    public function edit($id)
    {
        $fields = "create_time,delete_time,update_time";
        $where = [];
        $where[] = ['id', '=', $id];
        $banquet = $this->model->field($fields, true)->where($where)->order('id desc')->find();
        $packageList = Package::getList();
        $ritualList = Ritual::getList();
        if ($banquet) {
            $result = [
                'code'  => '200',
                'msg'   => '获取婚宴信息成功',
                'data'  => [
                    'detail'   => $banquet,
                    'packageList' => $packageList,
                    'ritualList' => $ritualList
                ]
            ];
        } else {
            $result = [
                'code'  => '400',
                'msg'   => '获取婚宴信息失败'
            ];
        }

        return json($result);
    }

    public function doEdit()
    {
        $params = $this->request->param();

        if (empty(!$params['id'])) {
            $where = [];
            $where[] = ['id', '=', $params['id']];
            $model = $this->model->where($where)->find();
            $result = $model->save($params);
        } else {
            $result = $this->model->allowField(true)->save($params);
        }

        if ($result) {
            $arr = ['code' => '200', 'msg' => '编辑基本信息成功'];
        } else {
            $arr = ['code' => '400', 'msg' => '编辑基本信息失败'];
        }

        return json($arr);
    }
}