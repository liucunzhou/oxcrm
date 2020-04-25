<?php
namespace app\index\controller\organization;

use app\common\model\AuthGroup;
use app\common\model\Brand;
use app\index\controller\Backend;

class Audit extends Backend
{
    protected $brands = [];
    protected $staffs = [];
    protected $roles = [];
    protected $sequence = [];

    protected function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\Audit();

        ## brands
        $this->brands = Brand::getBrands();
        $this->assign('brands', $this->brands);

        ## 员工列表
        $this->staffs = \app\common\model\User::getUsers(false);
        $this->assign('staffs', $this->staffs);

        ## 角色列表
        $this->roles = AuthGroup::getRoles();
        $this->assign('roles', $this->roles);

        ## 审核全局列表
        $config = config();
        $this->sequence = $config['crm']['check_sequence'];
        $this->assign('sequence', $this->sequence);
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

        $content = [];
        foreach ($request['rule'] as $row) {
            isset($request[$row]) && $content[$row] = $request[$row];
        }

        $this->model->content = json_encode($content);
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

        $selected = [];
        $csequence = json_decode($data->content, true);
        foreach ($csequence as $key=>$row) {
            $selected[$key] = [
                'id'    => $this->sequence[$key]['id'],
                'title' => $this->sequence[$key]['title'],
                'type'  => $this->sequence[$key]['type'],
                'items'  => $row
            ];
        }
        // print_r($selected);
        $this->assign('selected', $selected);

        $unselected = [];
        foreach ($this->sequence as $key=>$row) {
            if(!isset($csequence[$key])) {
                $unselected[$key] = $row;
            }
        }
        $this->assign('unselected', $unselected);
        return $this->fetch();
    }

    public function doEdit()
    {
        $request = $this->request->param();
        if(empty($request['company_id'])) {
            $result = [
                'code'  => '400',
                'msg'   => '请选择所属公司'
            ];
            return json($result);
        }

        $where = [];
        $where['id'] = $request['id'];
        $audit = $this->model->where($where)->find();

        $content = [];
        foreach ($request['rule'] as $row) {
            isset($request[$row]) && $content[$row] = $request[$row];
        }

        $audit->content = json_encode($content);
        $row = $audit->allowField(true)->save($request);
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
}