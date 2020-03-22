<?php

namespace app\wash\controller\dictionary;

use app\wash\controller\Backend;
use think\Request;

class Store extends Backend
{
    protected $customerModel;
    protected $regionModel;
    protected $levels = [];

    protected function initialize(){
        parent::initialize();

        $this->model = new \app\common\model\Store();
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        // 获取酒店信息
        $where = [];
        $where['id'] = $id;
        $store = $this->model->where($where)->find();
        $this->assign('store', $store);

        if(!empty($store->images)) {
            $images = explode(":::", $store->images);
            foreach($images as &$row) {
                $row = 'https://www.yusivip.com'.$row;
            }
        } else {
            $images = [];
        }
        $this->assign('images', $images);

        return $this->fetch();
    }

}
