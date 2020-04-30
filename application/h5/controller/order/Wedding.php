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

    public function edit($id)
    {
        $fields = "create_time,delete_time,update_time";
        $where = [];
        $where[] = ['id', '=', $id];
        $data = $this->model->field($fields, true)->where($where)->find();
        $packageList = Package::getList();
        $ritualList = Ritual::getList();
        $companyList = Brand::getBrands();

        $data['package_title'] = $packageList[$data->banquet_package_id]['title'];
        $data['ritual_title'] = $ritualList[$data->banquet_ritual_id]['title'];
        $data['company_title'] = $companyList[$data->company_id]['title'];
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

        if(empty(!$params['id'])) {
            $where = [];
            $where[] = ['id', '=', $params['id']];
            $model = $this->model->where($where)->find();
            $result = $model->save($params);
        } else {
            $result = $this->model->allowField(true)->save($params);
        }

        if($result) {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}