<?php

namespace app\wash\controller\customer;

use app\wash\controller\Backend;
use think\Request;

class Append extends Backend
{
    protected $customerModel;

    protected function initialize()
    {
        parent::initialize();

        $this->model = new \app\common\model\MemberAllocate();
        $this->customerModel = new \app\common\model\Member();
    }

    /**
     * 追加手机号
     */
    public function mobile()
    {

        return $this->fetch();
    }

    public function doAppendMobile()
    {

    }

    /**
     * 追加渠道
     */
    public function source()
    {
        return $this->fetch();
    }

    public function doAppendSource()
    {

    }

    /**
     * 加入酒店推荐
     */
    public function recommend()
    {
        $params = $this->request->param();
        $memberHotelSelected = new \app\common\model\MemberHotelSelected();
        $where = [];
        $where['allocate_id'] = $params['allocate_id'];
        $where['hotel_id'] = $params['hotel_id'];
        $selected = $memberHotelSelected->where($where)->find();
        if(!empty($selected)) return '';

        $where = [];
        $where['id'] = $params['allocate_id'];
        $allocate = $this->model->where($where)->find();
        $this->assign('allocate', $allocate);

        $data['allocate_id'] = $params['allocate_id'];
        $data['member_id'] = $allocate->member_id;
        $data['hotel_id'] = $params['hotel_id'];
        $data['create_time'] = time();
        $result = $memberHotelSelected->save($data);

        $storeModel = new \app\common\model\Store();
        $where = [];
        $where['id'] = $params['hotel_id'];
        $store = $storeModel->where($where)->find();
        $this->assign('store', $store);

        return $this->fetch();
    }

    /***
     * 分配到客户
     */
    public function allocate()
    {
        $params = $this->request->param();
        $memberHotelSelected = new \app\common\model\MemberHotelSelected();
        $memberHotelAllocate = new \app\common\model\MemberHotelAllocate();
        foreach($params['staff_id'] as $key=>$val) {
            $where = [];
            $where['member_id'] = $params['member_id'];
            $where['user_id'] = $val;
            // 检测是否已经分配客资给该客服
            $allocate = $this->model->where($where)->find();
            $allocate->startTrans();
            if(empty($allocate)) {
                // 分配客资到该客服
                $data = [];
                $data = $allocate->getData();
                // 修改分配方式
                unset($data['id']);
                $data['allocate_type'] = 0;
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $val;
                $result = $this->model->save($data);
                if(!$result) { 
                    $allocateId = 0;
                } else {
                    $allocateId = $this->model->id;
                }
            } else {
                $allocateId = $allocate->id;
            }
            
            if(!$allocateId) {
                $this->model->rollBack();
                return json([
                    'code'  => '500',
                    'msg'   => '分配客资失败'
                ]);
            }

            // 检测是否已经设置为推荐门店
            $where = [];
            $where['allocate_id'] = $params['allocate_id'];
            $where['store_id'] = $params['store_id'];
            $selected = $memberHotelSelected->where($where)->find();
            if(!empty($selected)) {
                $data = [];
                $data['allocate_id'] = $params['id'];
                $data['member_id'] = $params['member_id'];
                $data['hotel_id'] = $params['store_id'];
                $data['assigned'] = 1;
                $data['create_time'] = time();
                $selected = $memberHotelSelected->save();   
            } else {
                $selected->save(['assigned'=>1]);
            }
            $selectedId = $selected->id;
            if(!$allocateId) {
                $this->model->rollBack();
                return json([
                    'code'  => '500',
                    'msg'   => '推荐酒店失败'
                ]);
            }

            // 检测是否已经分配客资及门店到该客服
            $where = [];
            $where['from_allocate_id'] = $params['allocate_id'];
            $where['store_id'] = $params['member_id'];
            $where['staff_id'] = $val;
            $hotelAllocate =  $memberHotelAllocate->where($where)->find();
            if(empty($hotelAllocate)) {
                $data = [];
                $data['from_allocate_id'] = $params['allocate_id'];
                $data['allocate_id'] = $allocateId;
                $data['store_id'] = $params['member_id'];
                $data['staff_id'] = $val;
                $data['member_id'] = $params['member_id'];
                $data['create_time'] = time();
                $hotelAllocate = $memberHotelAllocate->save($data);
            }
            $hotelAllocateId = $hotelAllocate->id;
            if($hotelAllocateId) {
                $this->model->commit();
                $arr = [
                    'code'  => '200',
                    'msg'   => '分配成功'
                ];
            } else {
                $this->model->rollBack();
                $arr = [
                    'code'  => '200',
                    'msg'   => '分配失败'
                ];
            }
            
            return  json($arr);

        }
        
    }
}
