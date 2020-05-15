<?php
namespace app\h5\controller\order;


use app\common\model\MemberAllocate;
use app\common\model\User;
use app\h5\controller\Base;

class Prepay extends Base
{
    protected $staffs = [];
    protected $brands = [];
    protected $hotels = [];
    protected $carList = [];
    protected $sugarList = [];
    protected $wineList = [];
    protected $lightList = [];
    protected $dessertList = [];
    protected $ledList = [];
    protected $d3List = [];
    protected $packageList = [];
    protected $ritualList = [];
    protected $confirmStatusList = [0 => '待审核', 1 => '审核中', 2 => '审核通过', 3 => '审核驳回'];

    protected function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\Order();

        ## 获取所有品牌、公司
        $this->brands = \app\common\model\Brand::getBrands();

        ## 套餐列表
        $this->packageList = \app\common\model\Package::getList();

        ## 套餐列表
        $this->ritualList = \app\common\model\Ritual::getList();

        ## 汽车列表
        $this->carList = \app\common\model\Car::getList();

        ## 酒水列表
        $this->wineList = \app\common\model\Wine::getList();

        ## 喜糖列表
        $this->sugarList = \app\common\model\Sugar::getList();

        ## 灯光列表
        $this->lightList = \app\common\model\Light::getList();

        ## 点心列表
        $this->dessertList = \app\common\model\Dessert::getList();

        ## led列表
        $this->ledList = \app\common\model\Led::getList();

        ## 3d列表
        $this->d3List = \app\common\model\D3::getList();

        if (isset($this->role['auth_type']) && $this->role['auth_type'] > 0) {
            $this->staffs = User::getUsersByDepartmentId($this->user['department_id']);
        }
    }

    # 创建订单
    public function create()
    {
        $result = [
            'code'  => '200',
            'msg'   => '获取数据成功',
            'data'  => [
                'companyList' => array_values($this->brands),    ##签约公司列表
                'newsTypeList' => array_values($this->config['news_type_list']),
            ]
        ];

        return json($result);
    }

    # 创建订单
    public function doCreate()
    {
        $param = $this->request->param();
        $allocate = MemberAllocate::get($param['id']);
        $member = \app\api\model\Member::get($allocate->member_id);

        ### 格式化数据
        $orderData = json_decode($param['order'], true);
        $orderData['news_type'] = $orderData['newsType'];
        $orderData['member_id'] = $member->id;
        $orderData['realname'] = $member->realname;
        $orderData['mobile'] = $member->mobile;
        $orderData['source_id'] = $member->source_id;
        $orderData['source_text'] = $member->source_text;
        $orderData['operate_id'] = $this->user['id'];
        $orderData['user_id'] = $this->user['id'];
        $orderData['salesman'] = $this->user['id'];
        $orderData['image'] = empty($orderData['imageArray']) ? '' : implode(',', $orderData['imageArray']);

        $OrderModel = new \app\common\model\Order();
        $result = $OrderModel->allowField(true)->save($orderData);
        if (!$result || !isset($OrderModel->id)) {
            return json(['code' => '400', 'msg' => '创建失败']);
        }
        $source['order'] = $OrderModel->toArray();


        // 根据公司创建审核流程
        create_order_confirm($OrderModel->id, $orderData['company_id'], $this->user['id'], 'prepay', "创建意向金", $source);
        return json(['code' => '200', 'msg' => '创建成功']);
    }

}