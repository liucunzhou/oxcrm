<?php

namespace app\index\controller\order;

use app\common\model\BanquetHall;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\OrderBanquet;
use app\common\model\OrderBanquetPayment;
use app\common\model\OrderBanquetReceivables;
use app\common\model\OrderBanquetSuborder;
use app\common\model\OrderCar;
use app\common\model\OrderConfirm;
use app\common\model\OrderD3;
use app\common\model\OrderDessert;
use app\common\model\OrderEntire;
use app\common\model\OrderHotelItem;
use app\common\model\OrderHotelProtocol;
use app\common\model\OrderLed;
use app\common\model\OrderLight;
use app\common\model\OrderSugar;
use app\common\model\OrderWedding;
use app\common\model\OrderWeddingPayment;
use app\common\model\OrderWeddingReceivables;
use app\common\model\OrderWeddingSuborder;
use app\common\model\OrderWine;
use app\common\model\Search;
use app\common\model\User;
use app\index\controller\Backend;
use app\index\controller\Base;
use app\index\controller\organization\Audit;
use think\facade\Request;

class Confirm extends Backend
{
    protected $hotels = [];
    protected $sources = [];
    protected $suppliers = [];
    protected $weddingDevices = [];
    protected $weddingCategories = [];
    protected $paymentTypes = [1=>'定金', 2=>'预付款', 3=>'尾款', 4=>'其他'];
    protected $payments = [1=>'现金', 2=>'POS机', 3=>'微信', 4=>'支付宝'];
    protected $confirmStatusList = [0=>'待审核', 1=>'通过', 2=>'驳回'];

    protected function initialize()
    {
        parent::initialize();
        $this->model = new OrderConfirm();

        // 获取系统来源,酒店列表,意向状态
        $this->assign('payments', $this->payments);
        $this->assign('paymentTypes', $this->paymentTypes);
        $this->assign('confirmStatusList', $this->confirmStatusList);

        $staffes = User::getUsersInfoByDepartmentId($this->user['department_id']);
        $this->assign('staffes', $staffes);

        $this->sources = \app\common\model\Source::getSources();
        $this->assign('sources', $this->sources);

        ## 获取所有品牌、公司
        $brands = \app\common\model\Brand::getBrands();
        $this->assign('brands', $brands);

        ## 酒店列表
        $this->hotels = \app\common\model\Store::getStoreList();
        $this->assign('hotels', $this->hotels);
        $this->assign('hotelsJson', json_encode($this->hotels));

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取套餐列表
        $packages = \app\common\model\Package::getList();
        $this->assign('packages', $packages);

        ## 获取仪式列表
        $rituals = \app\common\model\Ritual::getList();
        $this->assign('rituals', $rituals);

        ## 酒店服务项目
        $banquetHoteItems = \app\common\model\BanquetHotelItem::getList();
        $this->assign('banquetHoteItems', $banquetHoteItems);

        ## 酒店服务项目
        $cars = \app\common\model\Car::getList();
        $this->assign('cars', $cars);

        ## 供应商列表
        $this->suppliers = \app\common\model\Supplier::getList();
        $this->assign('suppliers', $this->suppliers);
        $this->assign('suppliersJson', json_encode($this->suppliers));

        ## 婚庆设备列表
        $this->weddingDevices = \app\common\model\WeddingDevice::getList();
        $this->assign('weddingDevices', $this->weddingDevices);

        ## 婚庆二销分类列表
        $this->weddingCategories = \app\common\model\WeddingCategory::getList();
        $this->assign('weddingCategories', $this->weddingDevices);
    }

    # 誉思
    public function index()
    {
        if (Request::isAjax()) {
            $get = $this->request->param();
            $get['company_id'] = 25;
            $list = $this->_getConfirmList($get);

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'data' => $list->getCollection(),
                'count' => $list->total()
            ];
            return json($result);

        } else {
            return $this->fetch('order/confirm/index');
        }
    }

    public function hs()
    {
        if (Request::isAjax()) {
            $get = $this->request->param();
            $get['company_id'] = 26;
            $list = $this->_getConfirmList($get);

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'data' => $list->getCollection(),
                'count' => $list->total()
            ];
            return json($result);

        } else {
            return $this->fetch('order/confirm/index');
        }
    }

    public function lk()
    {
        if (Request::isAjax()) {
            $get = $this->request->param();
            $get['company_id'] = 27;
            $list = $this->_getConfirmList($get);

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'data' => $list->getCollection(),
                'count' => $list->total()
            ];
            return json($result);

        } else {
            return $this->fetch('order/confirm/index');
        }
    }

    public function mangena()
    {
        if (Request::isAjax()) {
            $get = $this->request->param();
            $get['company_id'] = 24;
            $list = $this->_getConfirmList($get);

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'data' => $list->getCollection(),
                'count' => $list->total()
            ];
            return json($result);

        } else {
            return $this->fetch('order/confirm/index');
        }
    }

    # 查看订单信息
    public function showOrder()
    {
        $get = $this->request->param();
        $orderConfirm = $this->model->where('id', '=', $get['confirm_id'])->find();
        $this->assign('confirm', $orderConfirm);

        $get['id'] = $orderConfirm->order_id;
        if (empty($get['id'])) return false;
        $order = \app\common\model\Order::get($get['id']);
        if(empty($this->user['sale']) && $order->salesman > 0) {
            $sale = User::getUser($order->salesman);
            $order->sale = $sale['realname'];
        }
        $this->assign('data', $order);

        #### 获取婚宴订单信息
        $where = [];
        $where['order_id'] = $get['id'];
        $banquet = \app\common\model\OrderBanquet::where($where)->order('id desc')->find();
        $this->assign('banquet', $banquet);

        #### 酒店服务项目
        $where = [];
        $where['order_id'] = $get['id'];
        $hotelItem = \app\common\model\OrderHotelItem::where($where)->order('id desc')->find();
        $this->assign('hotelItem', $hotelItem);

        #### 获取婚宴二销订单信息
        $where = [];
        $where['order_id'] = $get['id'];
        $banquetOrders = \app\common\model\OrderBanquetSuborder::where($where)->select();
        $this->assign('banquetOrders', $banquetOrders);

        #### 获取婚宴收款信息
        $receivables = \app\common\model\OrderBanquetReceivables::where('order_id', '=', $get['id'])->select();
        $this->assign('banquetReceivables', $receivables);

        #### 获取婚宴付款信息
        $banquetPayments = \app\common\model\OrderBanquetPayment::where('order_id', '=', $get['id'])->select();
        $this->assign('banquetPayments', $banquetPayments);

        #### 获取酒店协议信息
        $hotelProtocol = \app\common\model\OrderHotelProtocol::where('order_id', '=', $get['id'])->select();
        $this->assign('hotelProtocol', $hotelProtocol);



        #### 获取婚庆订单信息
        $where = [];
        $where['order_id'] = $get['id'];
        $wedding = \app\common\model\OrderWedding::where($where)->order('id desc')->find();
        $this->assign('wedding', $wedding);
        #### 获取婚宴二销订单信息
        $where = [];
        $where['order_id'] = $get['id'];
        $weddingOrders = \app\common\model\OrderWeddingSuborder::where($where)->select();
        $this->assign('weddingOrders', $weddingOrders);

        #### 获取婚宴收款信息
        $weddingReceivables = \app\common\model\OrderWeddingReceivables::where('order_id', '=', $get['id'])->select();
        $this->assign('weddingReceivables', $weddingReceivables);
        #### 获取婚庆付款信息
        $weddingPayments = \app\common\model\OrderWeddingPayment::where('order_id', '=', $get['id'])->select();
        $this->assign('weddingPayments', $weddingPayments);

        #### 婚车
        $where = [];
        $where['order_id'] = $get['id'];
        $car = \app\common\model\OrderCar::where($where)->select();
        $this->assign('car', $car);

        #### 喜糖
        $where = [];
        $where['order_id'] = $get['id'];
        $sugar = \app\common\model\OrderSugar::where($where)->select();
        $this->assign('sugar', $sugar);

        #### 酒水
        $where = [];
        $where['order_id'] = $get['id'];
        $wine = \app\common\model\OrderWine::where($where)->select();
        $this->assign('wine', $wine);

        #### 灯光
        $where = [];
        $where['order_id'] = $get['id'];
        $light = \app\common\model\OrderLight::where($where)->select();
        $this->assign('light', $light);

        #### 点心
        $where = [];
        $where['order_id'] = $get['id'];
        $dessert = \app\common\model\OrderDessert::where($where)->select();
        $this->assign('dessert', $dessert);

        #### LED
        $where = [];
        $where['order_id'] = $get['id'];
        $led = \app\common\model\OrderLed::where($where)->select();
        $this->assign('led', $led);


        #### 3D
        $where = [];
        $where['order_id'] = $get['id'];
        $d3 = \app\common\model\OrderD3::where($where)->select();
        $this->assign('d3', $d3);


        ##　获取客资分配信息
        $allocate = \app\common\model\MemberAllocate::where('id', '=', $order['member_allocate_id'])->find();
        $this->assign('allocate', $allocate);

        ## 获取客户信息
        $member = \app\common\model\Member::get($order->member_id);
        if($member) $this->assign('member', $member);

        ## 宴会厅列表
        $halls = \app\common\model\BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = \app\common\model\User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);
        $where = [];
        $where[] = ['company_id', '=', $order->company_id];
        $where[] = ['timing', '=', $orderConfirm->confirm_type];
        $audit = \app\common\model\Audit::where($where)->find();

        $config = config();
        $sequences = $config['crm']['check_sequence'];
        if(!empty($sequences)) {
            $sequence = (array)json_decode($audit->content, true);
            foreach ($sequence as $key => &$row) {
                $where = [];
                $where[] = ['order_id', '=', $get['id']];
                $where[] = ['company_id', '=', $order->company_id];
                $where[] = ['confirm_no', '=', $orderConfirm->confirm_no];
                $where[] = ['confirm_type', '=', $orderConfirm->confirm_type];
                $where[] = ['confirm_item_id', '=', $key];
                $confirmModel = new OrderConfirm();
                $currentConfirm = $confirmModel->where($where)->find();
                $row['title'] = $sequences[$key]['title'];
                if (!empty($currentConfirm)) {
                    $cstatus = $currentConfirm->status;
                    $row['status'] = $this->confirmStatusList[$cstatus];
                    $row['content'] = $currentConfirm->content;
                } else {
                    $row['status'] = '待审核';
                    $row['content'] = '';
                }
            }
            $this->assign('sequence', $sequence);
        }

        return $this->fetch('order/show/main');
    }

    protected function _getConfirmList($get)
    {
        $config = [
            'page' => $get['page']
        ];
        $map = [];
        $map[] = ['company_id', '=', $get['company_id']];
        $map[] = ['confirm_user_id', '=', $this->user['id']];
        $list = $this->model->where($map)->order('id desc')->paginate($get['limit'], false, $config);

        $users = \app\common\model\User::getUsers();
        foreach ($list as $key => &$value) {
            $order = \app\common\model\Order::get($value['order_id']);
            $value['bridegroom_mobile'] = $order->bridegroom_mobile ? substr_replace($order->bridegroom_mobile, '***', 3, 3) : '-';
            $value['bride_mobile'] = $order->bride_mobile ? substr_replace($order->bride_mobile, '***', 3, 3) : '-';
            $value['user_id'] = isset($users[$value['user_id']]) ? $users[$value['user_id']]['realname'] : '-';
            $value['banquet_hall_name'] = $order->banquet_hall_name;
            $value['hotel_text'] = $order->hotel_text;
            $value['source_text'] = $order->source_text;
            $value['bridegroom'] = $order->bridegroom;
            $value['bride'] = $order->bride;
            $value['sign_date'] = $order->sign_date;
            $value['event_date'] = $order->event_date;
        }
        return $list;
    }

    # 来源-积分-合同审核确认，执行逻辑
    public function doAccepte()
    {
        $param = $this->request->param();

        ## 获取订单信息
        $confirm = $this->model->where('id', '=', $param['id'])->find();
        $data = $confirm->getData();
        $confirm->content = $param['content'];
        $confirm->status = 1;
        $confirm->operate_id = $this->user['id'];
        $result = $confirm->save();
        if($result) {
            $newConfirm = new OrderConfirm();
            $newConfirm->where('confirm_no', '=', $confirm->confirm_no)->update(['status'=>1,'is_checked'=>1]);

            ## 获取当前配置
            $where = [];
            $where[] = ['company_id', '=', $confirm->company_id];
            $where[] = ['timing', '=', $confirm->confirm_type];
            $auditModel = new \app\common\model\Audit();
            $audit = $auditModel->where($where)->find();
            $sequence = json_decode($audit->content, true);
            $current = $confirm->confirm_item_id;
            $next_confirm_item_id = get_next_confirm_item($current, $sequence);
            if(!is_null($next_confirm_item_id)) {
                $config = config();
                $auditConfig = $config['crm']['check_sequence'];
                unset($data['id']);
                unset($data['create_time']);
                unset($data['update_time']);
                unset($data['delete_time']);
                if($auditConfig[$next_confirm_item_id]['type'] == 'staff') {
                    // 指定人员审核
                    foreach ($sequence[$next_confirm_item_id] as $row)
                    {
                        $data['is_checked'] = 0;
                        $data['status'] = 0;
                        $data['confirm_item_id'] = $next_confirm_item_id;
                        $data['confirm_user_id'] = $row;
                        $orderConfirm = new \app\common\model\OrderConfirm();
                        $orderConfirm->allowField(true)->save($data);
                    }
                } else {
                    $user = \app\common\model\User::getUser($confirm->user_id);
                    // 指定角色审核
                    foreach ($sequence[$next_confirm_item_id] as $row) {
                        $staff = \app\common\model\User::getRoleManager($row, $user);
                        $data['is_checked'] = 0;
                        $data['status'] = 0;
                        $data['confirm_item_id'] = $next_confirm_item_id;
                        $data['confirm_user_id'] = $staff['id'];
                        $orderConfirm = new \app\common\model\OrderConfirm();
                        $orderConfirm->allowField(true)->save($data);
                    }
                }
                \app\common\model\Order::where('id', '=', $confirm->order_id)->update(['check_status'=>1]);
            } else {
                \app\common\model\Order::where('id', '=', $confirm->order_id)->update(['check_status'=>2]);
            }

            $json = ['code' => '200', 'msg' => '完成审核是否继续?'];
        } else {
            $json = ['code' => '500', 'msg' => '完成失败是否继续?'];
        }

        return json($json);
    }

    public function doReject()
    {
        $param = $this->request->param();

        ## 获取订单信息
        $confirm = $this->model->where('id', '=', $param['id'])->find();
        $orderId = $confirm->order_id;
        $confirm->content = $param['content'];
        $confirm->status = 2;
        $result = $confirm->save();
        $this->model->where('confirm_no', '=', $confirm->confirm_no)->save(['is_checked'=>1]);
        \app\common\model\Order::where('id', '=', $orderId)->update(['check_status'=>3]);
        if($result) {
            $json = ['code' => '200', 'msg' => '完成审核是否继续?'];
        } else {
            $json = ['code' => '500', 'msg' => '完成失败是否继续?'];
        }

        return json($json);
    }
}