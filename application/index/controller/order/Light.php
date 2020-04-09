<?php

namespace app\index\controller\order;


use app\common\model\OrderBanquet;
use app\index\controller\Backend;
use think\response\Json;

class Light extends Backend
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new OrderBanquet();

        ## 获取所有品牌、公司
        $brands = \app\common\model\Brand::getBrands();
        $this->assign('brands', $brands);


        $rituals = \app\common\model\Ritual::getList();
        $this->assign('rituals', $rituals);

        $packages = \app\common\model\Package::getList();
        $this->assign('packages', $packages);
    }

    public function edit($id)
    {

        $where = [];
        $where[] = ['order_id', '=', $id];
        $order = $this->model->where($where)->order('id desc')->find();
        $this->assign('data', $order);

        return $this->fetch();
    }

    public function doEdit()
    {
        $params = $this->request->param();

        $where = [];
        $where[] = ['id', '=', $params['id']];
        $banquet = $this->model->where($where)->find();

        $result = $banquet->save($params);

        if($result) {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}