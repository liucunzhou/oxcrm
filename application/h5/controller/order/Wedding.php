<?php
namespace app\h5\controller\order;

use app\common\model\Brand;
use app\common\model\OrderWedding;
use app\common\model\Package;
use app\common\model\Ritual;
use app\h5\controller\Base;

class Wedding extends Base
{
    protected $model = null;

    public function initialize()
    {
        parent::initialize();
        $this->model = new OrderWedding();
    }

    public function create()
    {
        $param = $this->request->param();
        $order = \app\common\model\Order::get($param['order_id']);
        if(empty($order)) {
            $result = [
                'code'  => '400',
                'msg'   => '订单不存在'
            ];
            return json($result);
        }

        $confirmList = $this->getConfirmProcess($order->company_id, 'order');

        $packageList = Package::getList();
        $ritualList = Ritual::getList();
        $companyList = Brand::getBrands();

        $result = [
            'code' => '200',
            'msg' => '获取数据成功',
            'data' => [
                'packageList' => array_values($packageList),
                'ritualList' => array_values($ritualList),
                'companyList' =>  array_values($companyList),
                'confirmList'   => $confirmList
            ]
        ];
        return json($result);
    }

    public function doCreate()
    {
        $params = $this->request->param();
        $orderId = $params['order_id'];
        $params = json_decode($params['wedding'], true);
        $weddingValidate = new \app\common\validate\OrderWedding();
        if(!$weddingValidate->check($params)) {
            return json([
                'code' => '400',
                'msg' => $weddingValidate->getError()
            ]);
        }

        $params['order_id'] = $orderId;
        $params['user_id'] = $this->user['id'];
        $result = $this->model->allowField(true)->save($params);
        $source['wedding'] = $this->model->toArray();

        if($result) {
            $order = \app\common\model\Order::get($orderId);
            $intro = "创建婚庆信息审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'创建婚庆信息成功'];
        } else {
            $arr = ['code'=>'400', 'msg'=>'创建婚庆信息失败'];
        }
        return json($arr);
    }

    public function edit($id)
    {
        $fields = "create_time,delete_time,update_time";
        $where = [];
        $where[] = ['id', '=', $id];
        $data = $this->model->field($fields, true)->where($where)->find();
        if(empty($data)) {
            $result = [
                'code' => '400',
                'msg' => '获取数据失败'
            ];
            return json($result);
        }
        $data = $data->getData();

        $packageList = Package::getList();
        $ritualList = Ritual::getList();
        $companyList = Brand::getBrands();

        $data['wedding_package_title'] = $packageList[$data['wedding_package_id']]['title'];
        $data['wedding_ritual_title'] = $ritualList[$data['wedding_ritual_id']]['title'];
        $data['company_title'] = $companyList[$data['company_id']]['title'];
        if($data) {
            $result = [
                'code' => '200',
                'msg' => '获取数据成功',
                'data' => [
                    'wedding' => $data,
                    'packageList' => array_values($packageList),
                    'ritualList' => array_values($ritualList),
                    'companyList' =>  array_values($companyList)
                ]
            ];
        } else {
            $result = [
                'code' => '400',
                'msg' => '获取数据失败'
            ];
        }
        return json($result);
    }

    public function doEdit()
    {
        $params = $this->request->param();
        $params = json_decode($params['wedding'], true);
        $params['item_check_status']  = 0;
        $weddingValidate = new \app\common\validate\OrderWedding();
        if(!$weddingValidate->check($params)) {
            return json([
                'code' => '400',
                'msg' => $weddingValidate->getError()
            ]);
        }


        if(!empty($params['id'])) {
            $where = [];
            $where[] = ['id', '=', $params['id']];
            $model = $this->model->where($where)->find();
            $result = $model->allowField(true)->save($params);
            $source['wedding'] = $model->toArray();
        } else {
            $result = $this->model->allowField(true)->save($params);
            $source['wedding'] = $this->model->toArray();
        }

        if($result) {
            $order = \app\common\model\Order::get($params['order_id']);
            $intro = "编辑婚庆信息审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'400', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}