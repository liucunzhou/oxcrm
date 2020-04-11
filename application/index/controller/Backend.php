<?php
namespace app\index\controller;


use think\Controller;

class Backend extends Controller
{
    public $user = [];
    public $model = null;
    protected $newsTypes = ['婚宴信息', '婚庆信息', '一站式','婚纱摄影','婚车','婚纱礼服','男装','宝宝宴','会务'];
    protected $paymentTypes = [1=>'定金', 2=>'中款', 3=>'尾款', 4=>'尾款'];
    protected $payments = [1=>'支付宝-对公', 2=>'支付宝-对私', 3=>'微信-对公', 4=>'微信-对私', 5=>'银行汇款-对公', 6=>'银行汇款-对私', 7=>'直付酒店', 8=>'现金', 9=>'POS机', 10=>'其他'];
    protected $suborderTypes = ['否', '婚宴二销', '婚庆二销'];

    protected function initialize()
    {
        // 验证登录
        $user = session("user");

        if (!$user) $this->redirect('/index/passport/login', ['parent' => 1]);
        $this->assign('newsTypes', $this->newsTypes);
        $this->assign('suborderTypes', $this->suborderTypes);

        $this->user = $user;
        $this->assign('user', $user);
    }

    public function create()
    {
        return $this->fetch();
    }

    public function doCreate()
    {
        $params = $this->request->param();
        $failMsg = '添加失败';
        if (!empty($this->validate)) {

            try {
                // $this->validate->check($params['row']);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                // dump($e->getError());
            }
        }

        $result = $this->model->save($params['row']);
        if ($result) {
            $arr = [
                'code'  => '200',
                'redirect' => $params['redirect'],
                'msg'   => '添加成功',
            ];
        } else {
            $arr = [
                'code'  => '500',
                'msg'   => $failMsg,
            ];
        }

        return json($arr);
    }

    public function edit($id)
    {
        $row = $this->model->find($id);

        $this->assign('data', $row);
    }

    public function doEdit()
    {
        $params = $this->request->param();

        $row = $this->model->find($params['id']);
        $result = $row->save($params);

        if ($result) {
            $arr = [
                'code'  => '200',
                'redirect' => $params['redirect'],
                'msg'   => '保存成功',
            ];
        } else {
            $arr = [
                'code'  => '500',
                'msg'   => '保存失败',
            ];
        }

        return json($arr);
    }

    public function delete($id)
    {
        $row = $this->model->find($id);
        $result = $row->delete();

        if ($result) {
            $arr = [
                'code'  => '200',
                'msg'   => '删除成功',
            ];
        } else {
            $arr = [
                'code'  => '500',
                'msg'   => '删除失败',
            ];
        }

        return json($arr);
    }
}