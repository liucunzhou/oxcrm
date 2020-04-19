<?php
namespace app\index\controller\organization;

use app\common\model\Brand;
use app\index\controller\Backend;
use think\response\Json;

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
            foreach ($list as &$row) {
                $row['company_id'] = $this->brands[$row['company_id']]['title'];
            }

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
        $request = $this->request->param();
        if(empty($request['company_id'])) {
            $result = [
                'code'  => '400',
                'msg'   => '请选择所属公司'
            ];
            return json($result);
        }

        $this->model->content = json_encode($request['rule']);
        $row = $this->model->allowField(true)->save($request);
        if($row) {
            $result = [
                'code'  => '200',
                'msg'   => '添加审核规则成功'
            ];
        } else {
            $result = [
                'code'  => '500',
                'msg'   => '添加审核规则失败'
            ];
        }

        return json($result);
    }

    public function edit($id)
    {
        $map = [];
        $map[] = ['id', '=', $id];
        $data = $this->model->where($map)->find();
        $this->assign('data', $data);

        $rules = json_decode($data['content'], true);
        $this->assign('rules', $rules);

        return $this->fetch();
    }

    public function doEdit()
    {
        return josn([]);
    }
}