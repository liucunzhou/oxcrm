<?php
namespace app\index\controller\organization;

use app\common\model\Brand;
use app\index\controller\Backend;

class Audit extends Backend
{
    protected $brands = [];

    protected function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\Audit();

        ## brands
        $this->brands = Brand::getBrands();
        $this->assign('brands', $this->brands);
    }

    // 规则列表
    public function index()
    {
        if($this->request->isAjax()) {
            $request = $this->request->param();
            $config = [
                'page' => $request['page']
            ];

            $map = [];
            $list = $this->model->where($map)->order("sort desc")->paginate($request['limit'], false, $config);
            $result = [
                'code'  => 0,
                'msg'   => '获取数据成功',
                'count' => $list->total(),
                'data'  => $list->getCollection()
            ];
            return json($result);

        } else {

            return $this->fetch();
        }
    }


    public function create()
    {
        return $this->fetch();
    }

    public function doCreate()
    {
       return json([]);
    }

    public function edit($id)
    {
        return $this->fetch();
    }

    public function doEdit()
    {
        return josn([]);
    }
}