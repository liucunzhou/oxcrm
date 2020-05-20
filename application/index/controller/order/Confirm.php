<?php

namespace app\index\controller\order;

use app\common\model\BanquetHall;
use app\common\model\Brand;
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
    // protected $paymentTypes = [1=>'定金', 2=>'预付款', 3=>'尾款', 4=>'其他'];
    // protected $payments = [1=>'现金', 2=>'POS机', 3=>'微信', 4=>'支付宝'];
    protected $confirmProjectStatusList = [0 => '待审核', 1 => '审核中', 2 => '审核通过', 3 => '审核驳回', 13 => '审核撤销'];
    // 审核过程中的审核项的审核状态
    protected $confirmStatusList = [0 => '待审核', 1 => '审核通过', 2 => '审核驳回', 13 => '审核撤销'];
    protected $cooperationModes = [1 => '返佣单', 2 => '代收代付', 3 => '代收代付+返佣单', 4 => '一单一议'];

    protected function initialize()
    {
        parent::initialize();
        $this->model = new OrderConfirm();

        // 获取系统来源,酒店列表,意向状态
        $this->assign('payments', $this->payments);
        $this->assign('paymentTypes', $this->paymentTypes);
        $this->assign('confirmStatusList', $this->confirmStatusList);
        $this->assign('confirmProjectStatusList', $this->confirmProjectStatusList);
        $this->assign('cooperationModes', $this->cooperationModes);
        ## 列出所有审核状态
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

        ## 汽车列表
        $carList = \app\common\model\Car::getList();
        $this->assign('carList', $carList);

        ## 酒水列表
        $wineList = \app\common\model\Wine::getList();
        $this->assign('wineList', $wineList);

        ## 喜糖列表
        $sugarList = \app\common\model\Sugar::getList();
        $this->assign('sugarList', $sugarList);

        ## 灯光列表
        $lightList = \app\common\model\Light::getList();
        $this->assign('lightList', $lightList);

        ## 点心列表
        $dessertList = \app\common\model\Dessert::getList();
        $this->assign('dessertList', $dessertList);

        ## led列表
        $ledList = \app\common\model\Led::getList();
        $this->assign('ledList', $ledList);

        ## 3d列表
        $d3List = \app\common\model\D3::getList();
        $this->assign('d3List', $d3List);
    }

    # 我的审核
    public function all()
    {
        if (Request::isAjax()) {
            $get = $this->request->param();
            $list = $this->_getConfirmGroupList($get);

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

    # 我的审核
    public function mine()
    {
        if (Request::isAjax()) {
            $get = $this->request->param();
            $list = $this->_getConfirmList($get);

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'data' => $list->getCollection(),
                'count' => $list->total()
            ];
            return json($result);

        } else {
            $confirmIntroList = $this->model->field('confirm_intro')->group('confirm_intro')->select();
            $this->assign('confirmIntroList', $confirmIntroList);
            return $this->fetch('order/confirm/index');
        }
    }

    # 待审核
    public function index()
    {
        if (Request::isAjax()) {
            $get = $this->request->param();
            $get['status'] = '0';
            $list = $this->_getConfirmList($get);

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'data' => $list->getCollection(),
                'count' => $list->total()
            ];
            return json($result);

        } else {
            $confirmIntroList = $this->model->field('confirm_intro')->group('confirm_intro')->select();
            $this->assign('confirmIntroList', $confirmIntroList);
            return $this->fetch('order/confirm/index');
        }
    }

    # 审核通过
    public function accept()
    {
        if (Request::isAjax()) {
            $get = $this->request->param();
            $get['status'] = 1;
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

    # 审核驳回
    public function reject()
    {
        if (Request::isAjax()) {
            $get = $this->request->param();
            $get['status'] = 2;
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

    # 审核撤销
    public function backend()
    {
        if (Request::isAjax()) {
            $get = $this->request->param();
            $get['status'] = 13;
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
        if (empty($this->user['sale']) && $order->salesman > 0) {
            $sale = User::getUser($order->salesman);
            $order->sale = $sale['realname'];
        }
        $this->assign('data', $order);

        #### 获取婚宴订单信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $banquet = \app\common\model\OrderBanquet::where($where)->order('id desc')->find();
        $this->assign('banquet', $banquet);

        #### 酒店服务项目
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $hotelItem = \app\common\model\OrderHotelItem::where($where)->order('id desc')->find();
        $this->assign('hotelItem', $hotelItem);

        #### 获取婚宴二销订单信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $banquetOrders = \app\common\model\OrderBanquetSuborder::where($where)->select();
        $this->assign('banquetOrders', $banquetOrders);

        #### 获取婚宴收款信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $receivables = \app\common\model\OrderBanquetReceivables::where($where)->select();
        foreach ($receivables as $key => &$row) {
            $contractImg = !empty($row['contract_img']) ? explode(',', $row['contract_img']) : [];
            $receiptImg = !empty($row['receipt_img']) ? explode(',', $row['receipt_img']) : [];
            $noteImg = !empty($row['note_img']) ? explode(',', $row['note_img']) : [];
            $photos = array_merge($contractImg, $receiptImg, $noteImg);
            $images = [];
            foreach ($photos as $key => $val) {
                $images[$key]['alt'] = '';
                $images[$key]['pid'] = $order['id'];
                $images[$key]['src'] = $val;
                $images[$key]['thumb'] = $val;
            }
            $imagesFormat = [
                'id' => $order['id'],
                'title' => '凭证',
                'start' => 0,
                'data' => $images
            ];
            $row['images'] = $imagesFormat;
        }
        $this->assign('banquetReceivables', $receivables);

        #### 获取婚宴付款信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $banquetPayments = \app\common\model\OrderBanquetPayment::where($where)->select();
        foreach ($banquetPayments as $key => &$row) {
            $receiptImg = !empty($row['receipt_img']) ? explode(',', $row['receipt_img']) : [];
            $noteImg = !empty($row['note_img']) ? explode(',', $row['note_img']) : [];
            $photos = array_merge($receiptImg, $noteImg);
            $images = [];
            foreach ($photos as $key => $val) {
                $images[$key]['alt'] = '';
                $images[$key]['pid'] = $order['id'];
                $images[$key]['src'] = $val;
                $images[$key]['thumb'] = $val;
            }
            $imagesFormat = [
                'id' => $order['id'],
                'title' => '凭证',
                'start' => 0,
                'data' => $images
            ];
            $row['images'] = $imagesFormat;
        }
        $this->assign('banquetPayments', $banquetPayments);

        #### 获取酒店协议信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $hotelProtocol = \app\common\model\OrderHotelProtocol::where($where)->select();
        $this->assign('hotelProtocol', $hotelProtocol);

        #### 获取婚庆订单信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $wedding = \app\common\model\OrderWedding::where($where)->order('id desc')->find();
        $this->assign('wedding', $wedding);

        #### 获取婚宴二销订单信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $weddingOrders = \app\common\model\OrderWeddingSuborder::where($where)->select();
        $this->assign('weddingOrders', $weddingOrders);

        #### 获取婚宴收款信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $weddingReceivables = \app\common\model\OrderWeddingReceivables::where($where)->select();
        foreach ($weddingReceivables as $key => &$row) {
            $contractImg = !empty($row['contract_img']) ? explode(',', $row['contract_img']) : [];
            $receiptImg = !empty($row['receipt_img']) ? explode(',', $row['receipt_img']) : [];
            $noteImg = !empty($row['note_img']) ? explode(',', $row['note_img']) : [];
            $photos = array_merge($contractImg, $receiptImg, $noteImg);
            $images = [];
            foreach ($photos as $key => $val) {
                $images[$key]['alt'] = '';
                $images[$key]['pid'] = $order['id'];
                $images[$key]['src'] = $val;
                $images[$key]['thumb'] = $val;
            }
            $imagesFormat = [
                'id' => $order['id'],
                'title' => '凭证',
                'start' => 0,
                'data' => $images
            ];
            $row['images'] = $imagesFormat;
        }
        $this->assign('weddingReceivables', $weddingReceivables);

        #### 获取婚庆付款信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $weddingPayments = \app\common\model\OrderWeddingPayment::where($where)->select();
        foreach ($weddingPayments as $key => &$row) {
            $receiptImg = !empty($row['receipt_img']) ? explode(',', $row['receipt_img']) : [];
            $noteImg = !empty($row['note_img']) ? explode(',', $row['note_img']) : [];
            $photos = array_merge($receiptImg, $noteImg);
            $images = [];
            foreach ($photos as $key => $val) {
                $images[$key]['alt'] = '';
                $images[$key]['pid'] = $order['id'];
                $images[$key]['src'] = $val;
                $images[$key]['thumb'] = $val;
            }
            $imagesFormat = [
                'id' => $order['id'],
                'title' => '凭证',
                'start' => 0,
                'data' => $images
            ];
            $row['images'] = $imagesFormat;
        }
        $this->assign('weddingPayments', $weddingPayments);

        #### 婚车
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $car = \app\common\model\OrderCar::where($where)->select();
        $this->assign('car', $car);

        #### 喜糖
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $sugar = \app\common\model\OrderSugar::where($where)->select();
        $this->assign('sugar', $sugar);

        #### 酒水
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $wine = \app\common\model\OrderWine::where($where)->select();
        $this->assign('wine', $wine);

        #### 灯光
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $light = \app\common\model\OrderLight::where($where)->select();
        $this->assign('light', $light);

        #### 点心
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $dessert = \app\common\model\OrderDessert::where($where)->select();
        $this->assign('dessert', $dessert);

        #### LED
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $led = \app\common\model\OrderLed::where($where)->select();
        $this->assign('led', $led);


        #### 3D
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $d3 = \app\common\model\OrderD3::where($where)->select();
        $this->assign('d3', $d3);

        ## 获取客户信息
        if (!empty($order->member_id)) {
            $member = \app\common\model\Member::get($order->member_id);
            $this->assign('member', $member);
        }

        ## 宴会厅列表
        $halls = \app\common\model\BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = \app\common\model\User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        ## 获取审核进度
        $where = [];
        $where[] = ['company_id', '=', $orderConfirm->company_id];
        $where[] = ['timing', '=', $orderConfirm->confirm_type];
        $audit = \app\common\model\Audit::where($where)->find();

        $where = [];
        $where['confirm_no'] = $orderConfirm->confirm_no;
        $where['order_id'] = $orderConfirm->order_id;
        $orderConfirm = new OrderConfirm();
        $confirmRs = $orderConfirm->where($where)->column('id,status,content,update_time,create_time,image', 'confirm_item_id');
        $avatar = 'https://www.yusivip.com/upload/commonAppimg/hs_app_logo.png';
        $staffs = User::getUsers(false);
        $config = config();
        $sequence = $config['crm']['check_sequence'];
        $auth = json_decode($audit->content, true);

        $userId = empty($order['user_id']) ? $order['salesman'] : $order['user_id'];
        $orderUser = User::getUser($userId);
        $confirmList = [];
        foreach ($auth as $key => $row) {
            $managerList = [];
            $type = $sequence[$key]['type'];
            if ($type == 'role') {
                // 获取角色
                foreach ($row as $v) {
                    $user = User::getRoleManager($v, $orderUser);
                    $managerList[] = [
                        'id' => $user['id'],
                        'realname' => $user['realname'],
                        'avatar' => $user['avatar'] ? $user['avatar'] : $avatar
                    ];
                }
            } else {
                foreach ($row as $v) {
                    if (!isset($staffs[$v])) continue;
                    $user = $staffs[$v];
                    $managerList[] = [
                        'id' => $user['id'],
                        'realname' => $user['realname'],
                        'avatar' => $user['avatar'] ? $user['avatar'] : $avatar
                    ];
                }
            }

            if ($confirmRs[$key]) {
                switch ($confirmRs[$key]['status']) {
                    case 0:
                        $status = '待审核';
                        break;
                    case 1:
                        $status = '审核通过';
                        break;
                    case 2:
                        $status = '审核驳回';
                        break;
                    case 13:
                        $status = '审核撤销';
                        break;
                    default:
                        $status = '待审核';
                }
                $content = $confirmRs[$key]['content'];
                $confirmTime = date('m-d H:i',$confirmRs[$key]['update_time']);
                $image = empty($confirmRs[$key]['image']) ? [] : explode(',', $confirmRs[$key]['image']);
            } else {
                $status = '待审核';
                $content = '';
                $confirmTime = '';
                $image = [];
            }

            $confirmList[] = [
                'id' => $key,
                'title' => $sequence[$key]['title'],
                'status' => $status,
                'content' => $content,
                'image' => $image,
                'confirm_time' => $confirmTime,
                'managerList' => $managerList
            ];
        }
        // print_r($confirmList);
        $this->assign('confirmList', $confirmList);

        ## 合同
        if (!empty($order['image'])) {
            $order['image'] = explode(',', $order['image']);
        } else {
            $order['image'] = [];
        }
        ## 收据
        if (!empty($order['receipt_img'])) {
            $order['receipt_img'] = explode(',', $order['receipt_img']);
        } else {
            $order['receipt_img'] = [];
        }
        ## 小票
        if (!empty($order['note_img'])) {
            $order['note_img'] = explode(',', $order['note_img']);
        } else {
            $order['note_img'] = [];
        }
        $this->assign('data', $order);
        $photos = [];
        $images = array_merge($order['image'], $order['receipt_img'], $order['note_img']);
        foreach ($images as $key => $val) {
            $photos[$key]['alt'] = '';
            $photos[$key]['pid'] = $order['id'];
            $photos[$key]['src'] = $val;
            $photos[$key]['thumb'] = $val;
        }
        $photosData = [
            'id' => $order['id'],
            'title' => '订单凭证',
            'start' => 0,
            'data' => $photos
        ];
        $this->assign('photosData', $photosData);
        $this->assign('images', $images);

        // 统计
        ### 婚庆总计
        if (empty($weddingOrderArr)) {
            $count['wedding_totals'] = 0;
        } else {
            $weddingTotalsArr = array_column($weddingOrderArr, 'wedding_totals');
            $count['wedding_totals'] = array_sum($weddingTotalsArr);
        }
        ### 婚宴总计
        if (empty($banquetOrderArr)) {
            $count['banquet_totals'] = 0;
        } else {
            $banquetTotalsArr = array_column($banquetOrderArr, 'banquet_totals');
            $count['banquet_totals'] = array_sum($banquetTotalsArr);
        }
        ### 订单综合
        $count['totals'] = $order['totals'];
        $count['customer_totals'] = $count['totals'] + $count['wedding_totals'] + $count['banquet_totals'];
        $this->assign('count', $count);

        return $this->fetch('order/show/main');
    }

    protected function _getConfirmList($get)
    {
        $config = [
            'page' => $get['page']
        ];
        $map = [];

        if ($get['staff'] > 0) {
            $map[] = ['user_id', '=', $get['staff']];
        }

        if (isset($get['source']) && !empty($get['source'])) {
            $map[] = ['source_id', '=', $get['source']];
        }

        if (!empty($get['company_id'])) {
            $map[] = ['company_id', '=', $get['company_id']];
        }
        if ($get['status'] != '') {
            $map[] = ['status', '=', $get['status']];
        }

        if ($get['confirm_intro'] != '') {
            $map[] = ['confirm_intro', '=', $get['confirm_intro']];
        }

        if ($this->user['nickname'] != 'admin') {
            $map[] = ['confirm_user_id', '=', $this->user['id']];
        }

        $configCrm = config();
        $checkSequence = $configCrm['crm']['check_sequence'];
        $brands = Brand::getBrands();
        $model = $this->model->where($map);
        if ($get['mobile'] != '') {
            $model = $model->where('order_id', 'in', function ($query) use ($get) {
                $query->table('tk_order')->where('bridegroom_mobile|bride_mobile', 'like', "%{$get['mobile']}%")->field('id');
            });
        }

        if ($get['date_range']!= '' && $get['date_range_type'] != '') {
            $model = $model->where('order_id', 'in', function ($query) use ($get) {
                $range = format_confirm_range($get['date_range']);
                $query->table('tk_order')->where($get['date_range_type'], 'between', $range)->field('id');
            });
        }

        if ($get['hotel_id'] > 0) {
            $map[] = ['hotel_id', '=', $get['hotel_id']];
            $model = $model->where('order_id', 'in', function ($query) use ($get) {
                $query->table('tk_order')->where('hotel_id', '=', $get['hotel_id'])->field('id');
            });
        }

        $list = $model->order('id desc')->paginate($get['limit'], false, $config);
        $users = \app\common\model\User::getUsers();
        foreach ($list as $key => &$value) {
            $companyId = $value->company_id;
            $value['company'] = $brands[$companyId]['title'];
            // $value['item'] = $checkSequence[$item]['title'];
            $value['item'] = $value->confirm_intro;
            $value['status'] = $this->confirmStatusList[$value['status']];
            $order = \app\common\model\Order::get($value['order_id']);
            if(empty($order)) unset($list[$key]);

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
            $value['banquet_hall_name'] = $order->banquet_hall_name;
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
        $confirm->image = $param['image'];
        $result = $confirm->save();
        if ($result) {
            $newConfirm = new OrderConfirm();
            $newConfirm->where('confirm_no', '=', $confirm->confirm_no)->update(['status' => 1, 'is_checked' => 1]);

            ## 获取当前配置
            $where = [];
            $where[] = ['company_id', '=', $confirm->company_id];
            $where[] = ['timing', '=', $confirm->confirm_type];
            $auditModel = new \app\common\model\Audit();
            $audit = $auditModel->where($where)->find();
            $sequence = json_decode($audit->content, true);
            $current = $confirm->confirm_item_id;
            $next_confirm_item_id = get_next_confirm_item($current, $sequence);
            if (!is_null($next_confirm_item_id)) {
                $config = config();
                $auditConfig = $config['crm']['check_sequence'];
                unset($data['id']);
                unset($data['create_time']);
                unset($data['update_time']);
                unset($data['delete_time']);
                if ($auditConfig[$next_confirm_item_id]['type'] == 'staff') {
                    // 指定人员审核
                    foreach ($sequence[$next_confirm_item_id] as $row) {
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
                \app\common\model\Order::where('id', '=', $confirm->order_id)->update(['check_status' => 1]);
                $this->updateItemStatus($confirm->source, 1);
            } else {

                \app\common\model\Order::where('id', '=', $confirm->order_id)->update(['check_status' => 2]);
                $this->updateItemStatus($confirm->source, 2);
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
        $confirm->image = $param['image'];
        $result = $confirm->save();
        $this->model->where('confirm_no', '=', $confirm->confirm_no)->update(['is_checked' => 1]);
        \app\common\model\Order::where('id', '=', $orderId)->update(['check_status' => 3]);
        $this->updateItemStatus($confirm->source, 3);
        if ($result) {
            $json = ['code' => '200', 'msg' => '完成审核是否继续?'];
        } else {
            $json = ['code' => '500', 'msg' => '完成失败是否继续?'];
        }
        return json($json);
    }

    protected function updateItemStatus($origin, $status)
    {
        if (empty($origin)) return false;
        $origin = json_decode($origin, true);

        $data['item_check_status'] = $status;
        foreach ($origin as $key => $row) {
            $whereId = [];
            $whereId['id'] = $row['id'];
            switch ($key) {
                case 'order':
                    \app\common\model\Order::where($whereId)->update($data);
                    break;

                case 'banquet':
                    OrderBanquet::where($whereId)->update($data);
                    break;

                case 'banquetIncome':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        OrderBanquetReceivables::where($where)->update($data);
                    }
                    break;

                case 'banquetPayment':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        OrderBanquetPayment::where($where)->update($data);
                    }
                    break;

                case 'banquetSuborder':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        OrderBanquetSuborder::where($where)->update($data);
                    }
                    break;

                case 'wedding':
                    OrderWedding::where($whereId)->update($data);
                    break;

                case 'weddingIncome':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        OrderWeddingReceivables::where($where)->update($data);
                    }
                    break;

                case 'weddingPayment':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        OrderWeddingPayment::where($where)->update($data);
                    }
                    break;

                case 'weddingSuborder':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        OrderWeddingSuborder::where($where)->update($data);
                    }
                    break;

                case 'hotelItem':
                    OrderHotelItem::where($whereId)->update($data);
                    break;

                case 'hotelProtocol':
                    OrderHotelProtocol::where($whereId)->update($data);
                    break;

                case 'car':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        OrderCar::where($where)->update($data);
                    }
                    break;

                case 'wine':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        OrderWine::where($where)->update($data);
                    }
                    break;

                case 'sugar':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        OrderSugar::where($where)->update($data);
                    }
                    break;

                case 'dessert':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        OrderDessert::where($where)->update($data);
                    }
                    break;

                case 'light':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        OrderLight::where($where)->update($data);
                    }
                    break;

                case 'led':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        OrderLed::where($where)->update($data);
                    }
                    break;

                case 'd3':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        OrderD3::where($where)->update($data);
                    }
                    break;
            }
        }
    }

    public function upload()
    {
        $file = request()->file("file");
        $info = $file->move("uploads/order");
        if ($info) {
            $origin = $info->getInfo();
            $data = [];
            $data['origin_file_name'] = $origin['name'];
            $data['new_file_name'] = $info->getFileName();
            $data['new_file_path'] = $info->getPathname();
            $arr = [
                'code' => '200',
                'msg' => '上传成功',
                'image' => '/' . $info->getPathname()
            ];
        } else {
            $arr = [
                'code' => '500',
                'msg' => '上传失败'
            ];
        }

        return json($arr);
    }
}