<?php
namespace app\index\controller;

use app\common\model\Allocate;
use app\common\model\Csv;
use app\common\model\DispatchAllocate;
use app\common\model\DuplicateLog;
use app\common\model\Hotel;
use app\common\model\Intention;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\MemberApply;
use app\common\model\MemberVisit;
use app\common\model\MobileRelation;
use app\common\model\Notice;
use app\common\model\OperateLog;
use app\common\model\Recommender;
use app\common\model\Region;
use app\common\model\Search;
use app\common\model\Source;
use app\common\model\Store;
use app\common\model\Tab;
use app\common\model\User;
use app\common\model\UserAuth;
use think\facade\Request;
use think\facade\Session;
use think\response\Download;

class Customer extends Base
{
    protected $newsTypes = ['婚宴信息', '婚庆信息', '一站式'];
    protected $washStatus = [];
    protected $hotels = [];
    protected $sources = [];
    protected $status = [];
    protected $auth = [];
    protected $budgets = [];
    protected $scales = [];

    protected function initialize()
    {
        parent::initialize();
        // 获取系统来源,酒店列表,意向状态
        $this->sources = Source::getSources();
        $this->hotels = Store::getStoreList();
        $this->status = Intention::getIntentions();
        $this->auth = UserAuth::getUserLogicAuth($this->user['id']);
        $this->budgets = \app\common\model\Budget::getBudgetList();
        $this->scales = \app\common\model\Scale::getScaleList();

        if(!Request::isAjax()) {
            $staffes = User::getUsersInfoByDepartmentId($this->user['department_id']);
            $this->assign('staffes', $staffes);
            $this->assign('sources', $this->sources);
        }
    }

    /**
     * 我的客资
     * @return mixed|\think\response\Json
     */
    public function mine()
    {
        if (Request::isAjax()) {
            $get = Request::param();
            $config = [
                'page' => $get['page']
            ];
            $map = Search::customerMine($this->user, $get);
            isset($get['keywords']) && $get['keywords'] = trim($get['keywords']);
            if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
                $map = [];
                $mobiles = MobileRelation::getMobiles($get['keywords']);
                if(!empty($mobiles)) {
                    $map[] = ['mobile', 'in', $mobiles];
                } else {
                    $map[] = ['mobile', '=', $get['keywords']];
                }
                $list = model('MemberAllocate')::hasWhere('member', $map, "Member.*")->order('id desc')->paginate($get['limit'], false, $config);
            } else if (isset($get['keywords']) && !empty($get['keywords']) && strlen($get['keywords']) < 11) {
                $map = [];
                $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
                $list = model('MemberAllocate')::hasWhere('member', $map, 'Member.*')->order('id desc')->with('member')->paginate($get['limit'], false, $config);
            } else if (isset($get['keywords']) && strlen($get['keywords']) > 11) {
                $map = [];
                $map[] = ['mobile', '=', $get['keywords']];
            } else {
                $list = model('MemberAllocate')->where($map)->order('id desc')->paginate($get['limit'], false, $config);
            }

            if(!empty($list)) {
                $users = User::getUsers();
                $data = $list->getCollection()->toArray();
                if(isset($get['keywords']) && strlen($get['keywords']) == 11) {
                    if(isset($data[0]) && !empty($data[0])) {
                        $data[0]['active_status'] = 0;
                        MemberAllocate::updateAllocateData($this->user['id'], $data[0]['id'], $data[0]);
                    }
                }

                foreach ($data as &$value) {
                    $value['allocate_time'] = $value['create_time'];
                    $value['user_realname'] = $users[$value['user_id']]['realname'];
                    if(!empty($get['keywords'])) {
                        $value['member_id'] = $value['id'];
                    }
                    if (empty($value['member'])){
                        $memberObj = Member::get($value['member_id']);
                        if($memberObj) {
                            $member = $memberObj->getData();
                        } else {
                            $member = [];
                        }
                    } else {
                        $member = $value['member'];
                        unset($value['member']);
                    }

                    if(empty($member)) continue;

                    $member['member_id'] = $member['id'];
                    unset($member['id']);
                    $value = array_merge($value, $member);
                    $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                    $value['news_type'] = $this->newsTypes[$value['news_type']];
                    $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
                    $value['intention_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                    $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                    if($this->auth['is_show_alias'] == '1') {
                        $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['alias'] : $value['source_text'];
                    } else {
                        $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : $value['source_text'];
                    }
                    $value['create_time'] = date('Y-m-d H:i', $member['create_time']);
                }
                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => $list->total(),
                    'data' => $data
                ];
            } else {
                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => 0,
                    'data' => []
                ];
            }
            return json($result);

        } else {
            $get = Request::param();
            $tabs = Tab::customerMine($this->user, $get);
            $this->assign('tabs', $tabs);
            $this->assign('get', $get);
            return $this->fetch();
        }
    }

    /**
     * 客资公海
     * @return mixed
     */
    public function seas()
    {
        $get = Request::param();
        if (Request::isAjax()) {
            $config = [
                'page' => $get['page']
            ];
            $map[] = ['is_sea', '=', '1'];
            if (isset($get['source']) && $get['source']>0) {
                $map[] = ['source_id', '=', $get['source']];
            }

            if (isset($get['staff']) && $get['staff'] > 0) {
                $map[] = ['user_id', '='. $get['staff']];
            }

            if (isset($get['date_range']) && !empty($get['date_range'])) {
                $range = explode('~', $get['date_range']);
                $range[0] = trim($range[0]);
                $range[1] = trim($range[1]);
                $start = strtotime($range[0]);
                $end = strtotime($range[1]);
                $map[] = ['create_time', 'between', [$start, $end]];
            }

            if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
                $map = [];
                $mobiles = MobileRelation::getMobiles($get['keywords']);
                if(!empty($mobiles)) {
                    $map[] = ['mobile', 'in', $mobiles];
                } else {
                    $map[] = ['mobile', '=', $get['keywords']];
                }
            } else if (isset($get['keywords']) && !empty($get['keywords']) && strlen($get['keywords']) < 11) {
                $map = [];
                $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
            } else if (isset($get['keywords']) && strlen($get['keywords']) > 11) {
                $map = [];
                $map[] = ['mobile', '=', $get['keywords']];
            }

            $list = model('Member')->where($map)->order('create_time desc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection()->toArray();

            if(isset($get['keywords']) && strlen($get['keywords']) == 11) {
                if(isset($data[0]) && !empty($data[0])) {
                    $data[0]['active_status'] = 0;
                    MemberAllocate::updateAllocateData($this->user['id'], $data[0]['id'], $data[0]);
                }
            }

            foreach ($data as &$value) {
                $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                $value['news_type'] = $this->newsTypes[$value['news_type']];
                $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                if($this->auth['is_show_alias'] == '1') {
                    $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['alias'] : $value['source_text'];
                } else {
                    $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : $value['source_text'];
                }
            }
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'pages' => $list->lastPage(),
                'count' => $list->total(),
                'data' => $data
            ];

            return json($result);

        } else {
            return $this->fetch();
        }
    }

    /**
     * 今日跟进
     * @return mixed
     */
    public function today()
    {
        if (Request::isAjax()) {
            $get = Request::param();
            $config = [
                'page' => $get['page']
            ];
            $map = Search::customerMine($this->user, $get);

            if (!isset($get['next_visit_time']) || empty($get['next_visit_time'])){
                $tomorrow = strtotime('tomorrow');
                $map[] = ['next_visit_time', '>', 0];
                $map[] = ['next_visit_time', '<', $tomorrow];
            }

            if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
                $map = [];
                $mobiles = MobileRelation::getMobiles($get['keywords']);
                if(!empty($mobiles)) {
                    $map[] = ['mobile', 'in', $mobiles];
                } else {
                    $map[] = ['mobile', '=', $get['keywords']];
                }
                $list = model('MemberAllocate')::hasWhere('member', $map, "Member.*")->order('id desc')->paginate($get['limit'], false, $config);
            } else if (isset($get['keywords']) && !empty($get['keywords']) && strlen($get['keywords']) < 11) {
                $map = [];
                $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
                $list = model('MemberAllocate')::hasWhere('member', $map, 'Member.*')->order('id desc')->with('member')->paginate($get['limit'], false, $config);
            } else if (isset($get['keywords']) && strlen($get['keywords']) > 11) {
                $map = [];
                $map[] = ['mobile', '=', $get['keywords']];
            } else {
                $list = model('MemberAllocate')->where($map)->order('next_visit_time desc')->paginate($get['limit'], false, $config);
            }

            if(!empty($list)) {
                $users = User::getUsers();
                $data = $list->getCollection()->toArray();
                if(isset($get['keywords']) && strlen($get['keywords']) == 11) {
                    if(isset($data[0]) && !empty($data[0])) {
                        $data[0]['active_status'] = 0;
                        MemberAllocate::updateAllocateData($this->user['id'], $data[0]['id'], $data[0]);
                    }
                }

                foreach ($data as &$value) {
                    $value['next_visit_time'] = date('Y-m-d H:i', $value['next_visit_time']);
                    $value['allocate_time'] = $value['create_time'];
                    $value['user_realname'] = $users[$value['user_id']]['realname'];
                    if(!empty($get['keywords'])) {
                        $value['member_id'] = $value['id'];
                    }
                    if (empty($value['member'])){
                        $memberObj = Member::get($value['member_id']);
                        if($memberObj) {
                            $member = $memberObj->getData();
                        } else {
                            $member = [];
                        }
                    } else {
                        $member = $value['member'];
                        unset($value['member']);
                    }

                    if(empty($member)) continue;

                    $member['member_id'] = $member['id'];
                    unset($member['id']);
                    $value = array_merge($value, $member);
                    $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                    $value['news_type'] = $this->newsTypes[$value['news_type']];
                    $value['hotel_id'] = $this->hotels[$value['hotel_id']]['title'];
                    $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
                    $len = strlen($value['budget']);
                    if($len < 2) {
                        $value['budget'] = isset($this->budgets[$value['budget']]) ? $this->budgets[$value['budget']]['title'] : $value['budget'];
                    }

                    $len = strlen($value['banquet_size']);
                    if($len < 2) {
                        $value['banquet_size'] = isset($this->scales[$value['banquet_size']]) ? $this->scales[$value['banquet_size']]['title'] : $value['banquet_size'];
                    }
                    $value['intention_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                    $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                    if($this->auth['is_show_alias'] == '1') {
                        $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['alias'] : $value['source_text'];
                    } else {
                        $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : $value['source_text'];
                    }
                    $value['create_time'] = date('Y-m-d H:i', $member['create_time']);
                }
                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => $list->total(),
                    'data' => $data
                ];

            } else {

                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => 0,
                    'data' => []
                ];
            }
            return json($result);

        } else {
            return $this->fetch('today');
        }
    }

    /**
     * 添加客资视图
     * @return mixed
     */
    public function addCustomer()
    {
        ### 客资来源
        $sources = \app\common\model\Source::getSources();
        $this->assign('sources', $sources);

        ### 获取推荐人列表
        $recommenders = \app\common\model\Recommender::column('id,title', 'id');
        $this->assign('recommenders', $recommenders);

        ### 酒店列表
        $hotels = Store::getStoreList();
        $this->assign("hotels", $hotels);


        $cities = Region::getCityList(0);
        $this->assign('cities', $cities);

        $areas = [];
        if($this->user['city_id']) {
            $areas = Region::getAreaList($this->user['city_id']);
        }
        $this->assign('areas', $areas);

        $data['area_id'] = [];
        $this->assign("data", $data);

        $action = 'add';
        $this->assign('action', $action);

        return $this->fetch('edit_customer');
    }

    /**
     * 编辑客资视图
     * @return mixed
     */
    public function editCustomer()
    {
        $get = Request::param();
        ### 来源列表
        $sources = \app\common\model\Source::getSources();
        $this->assign('sources', $sources);

        ### 获取推荐人列表
        $recommenders = \app\common\model\Recommender::column('id,title', id);
        $this->assign('recommenders', $recommenders);

        ### 酒店列表
        $hotels = Store::getStoreList();
        $this->assign("hotels", $hotels);

        $data = \app\common\model\Member::get($get['member_id']);
        if (!empty($data['area_id'])) $data['area_id'] = explode(',', $data['area_id']);
        $this->assign('data', $data);

        $cities = Region::getCityList(0);
        $this->assign('cities', $cities);

        $areas = [];
        if(!empty($this->user['city_id'])) {
            $areas = Region::getAreaList($this->user['city_id']);
        }
        $this->assign('areas', $areas);

        $action = 'edit';
        $this->assign('action', $action);
        return $this->fetch();
    }

    ### 编辑客户信息
    public function doEditCustomer()
    {
        $post = Request::post();

        if ($post['id']) {
            $action = '编辑客资';
            $Model = \app\common\model\Member::get($post['id']);
        } else {

            if (empty($post['mobile'])) {
                return json([
                    'code' => '400',
                    'msg' => '手机号不能为空',
                ]);
            }

            if (empty($post['realname'])) {
                return json([
                    'code' => '400',
                    'msg' => '客户姓名不能为空',
                ]);
            }

            $action = '添加客资';
            $Model = new \app\common\model\Member();
            $Model->member_no = date('YmdHis') . rand(100, 999);
        }

        ### 验证手机号唯一性
        $originMember = $Model::checkMobile($post['mobile']);
        if ($originMember) {
            return json([
                'code' => '400',
                'msg' => $post['mobile'] . '手机号已经存在',
            ]);
        }

        ### 开启事务
        $Model->startTrans();
        ### 同步来源名称
        if (isset($post['source_id']) && $post['source_id'] > 0) {
            $Model->source_text = $this->sources[$post['source_id']]['title'];
        }
        ### 基本信息入库
        $Model->is_sea = 1;
        $result1 = $Model->save($post);

        ### 新添加客资要加入到分配列表中
        if (empty($post['id'])) {
            MemberAllocate::updateAllocateData($this->user['id'], $Model->id, $post);
        }

        if ($result1) {
            ### 提交数据
            $Model->commit();

            ### 添加操作记录
            OperateLog::appendTo($Model);
            if(isset($Allocate)) OperateLog::appendTo($Allocate);
            return json(['code' => '200', 'msg' => $action . '成功']);
        } else {
            $Model->rollback();
            return json(['code' => '500', 'msg' => $action . '失败, 请重试']);
        }
    }

    ### 绑定客户信息
    public function bindCustomer(){
        if(Request::isPost()) {
            $post = Request::param();
            if (empty($post['mobiles'])) {
                return json([
                    'code' => '500',
                    'msg' => '请填写要绑定的手机号,至少两个'
                ]);
            }

            $mobiles = explode(',', $post['mobiles']);
            if(count($mobiles) < 1) {
                return json([
                    'code' => '500',
                    'msg' => '请填写要绑定的手机号,至少两个'
                ]);
            }

            ### 检测是否已经有绑定的手机号
            $hadMobiles = MobileRelation::getMobiles($post['target']);
            if(!empty($hadMobiles)) {
                $mobiles = array_merge($mobiles, $hadMobiles);
            } else {
                array_push($mobiles, $post['target']);
            }

            $mobilesStr = implode(',', $mobiles);
            $total = count($mobiles);
            $success = 0;
            foreach($mobiles as $key=>$mobile) {
                $data = [];
                $data['mobile'] = $mobile;
                $data['mobiles'] = $mobilesStr;
                $MobileRelation = new MobileRelation();
                $origin = $MobileRelation->where(['mobile'=>$mobile])->find();
                if(empty($origin)) {
                    $data['create_time'] = time();
                    $result = $MobileRelation->save($data);
                } else {
                    $result = $MobileRelation->save($data, ['mobile' => $mobile]);
                }

                if($result) {
                    $success = $success + 1;
                }
            }


            if($total == $success) {
                return json([
                    'code'  => '200',
                    'msg'   => '绑定号码成功'
                ]);
            } else {
                $failed = $total - $success;
                return json([
                    'code'  => '500',
                    'msg'   => '绑定成功{$success}个，失败{$failed}个'
                ]);
            }
        } else {
            $get = Request::param();
            $mobiles = MobileRelation::getMobiles($get['mobile']);
            $this->assign('mobiles', $mobiles);
            return $this->fetch();
        }
    }

    /**
     * 删除客资
     * @return \think\response\Json
     */
    public function deleteCustomer()
    {
        $get = Request::param();
        $result = \app\common\model\Member::get($get['id'])->delete();

        if ($result) {
            // 更新缓存
            \app\common\model\Member::updateCache();
            return json(['code' => '200', 'msg' => '删除成功']);
        } else {
            return json(['code' => '500', 'msg' => '删除失败']);
        }
    }

    public function apply()
    {
        $get = Request::param();
        if (Request::isAjax()) {
            $config = [
                'page' => $get['page']
            ];
            $map[] = ['user_id', '=', $this->user['id']];
            $status = !isset($get['status']) || $get['status'] == 0 ? 0 : $get['status'];
            $map[] = ['apply_status', '=', $status];

            $list = model('MemberApply')->where($map)->with('member')->paginate($get['limit'], false, $config);
            $data = $list->getCollection()->toArray();
            foreach ($data as &$value) {
                $member = $value['member'];
                unset($member['id']);
                unset($value['member']);
                $value = array_merge($value, $member);
                $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                $value['news_type'] = $this->newsTypes[$value['news_type']];
                $value['hotel_id'] = $this->hotels[$value['hotel_id']]['title'];
                if($this->auth['is_show_alias'] == '1') {
                    $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['alias'] : '未知';
                } else {
                    $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : '未知';
                }
            }

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $data
            ];
            return json($result);

        } else {

            $tabs = Tab::customerApply($get);
            $this->assign('tabs', $tabs);
            return $this->fetch();
        }
    }

    /**
     * 申请客资回访
     * @return \think\response\Json
     */
    public function doApply()
    {
        $ids = Request::post("ids");
        $ids = explode(',', $ids);
        if (empty($ids)) {
            return json([
                'code' => '500',
                'msg' => '申请的客资不能为空'
            ]);
        }

        $count = count($ids);
        $success = 0;
        foreach ($ids as $id) {
            $allocate = MemberAllocate::getAllocate($this->user['id'], $id);
            if (!empty($allocate)) continue;

            $Model = new MemberApply();
            $map = [];
            $map[] = ['user_id', '=', $this->user['id']];
            $map[] = ['member_id', '=', $id];
            $map[] = ['apply_status', '=', 0];
            $apply = $Model->where($map)->find();
            if($apply) continue;

            $data['user_id'] = $this->user['id'];
            $data['member_id'] = $id;
            $data['apply_status'] = 0;
            $data['create_time'] = time();
            $res = $Model->insert($data);
            if ($res) $success = $success + 1;
        }

        $fail = $count - $success;
        return json([
            'code' => '200',
            'msg' => "申请成功{$success}条,失败{$fail}条"
        ]);
    }

    public function checkMobile()
    {
        $post = Request::post();
        if(!isset($post['mobile'])) {
            return json([
                'code'   => '500',
                'msg'    => '请输入手机号'
            ]);
        }

        ### 手机号验证
        $len = strlen($post['mobile']);
        if($len != 11){
            return json([
                'code'   => '501',
                'msg'    => '请输入正确的手机号'
            ]);
        }

        ###  根据手机号获取用户信息
        $member = Member::getByMobile($post['mobile']);
        if(!empty($member)) {
            $isWriteDuplicate =false;
            $repeat = $this->sources[$post['source_id']]['title'];
            if(isset($this->sources[$post['source_id']]['parent_id'])) {
                $platformId = $this->sources[$post['source_id']]['parent_id'];
                $repeatPlatform = $this->sources[$platformId]['title'];
            } else {
                $repeatPlatform = '';
            }

            if(empty($member->repeat_log)) {
                $isWriteDuplicate = true;
            } else {
                ### 检测是否已存在title
                if(!empty($member->repeat_log) && !empty($repeat) && mb_strpos($member->repeat_log, $repeat)===false) {
                    $repeat = $member->repeat_log.','.$repeat;
                    $isWriteDuplicate = true;

                    if(!empty($member->repeat_platform_log) && !empty($repeatPlatform) && mb_strpos($member->repeat_platform_log, $repeatPlatform)===false) {
                        $repeatPlatform = $member->repeat_platform_log.','.$repeatPlatform;
                    }
                }
            }

            if($isWriteDuplicate) {
                $member->save(['repeat_log' => $repeat, 'repeat_platform_log'=>$repeatPlatform]);
                $map = [];
                $map[] = ['user_id', '=', $this->user['id']];
                $map[] = ['member_id', '=', $member->id];
                $map[] = ['source_id', '=', $post['source_id']];
                $repeatLogData = DuplicateLog::where($map)->find();

                if(empty($repeatLogData)) {
                    ### 添加回访日志
                    $data = [];
                    $data['user_id'] = $this->user['id'];
                    $data['member_id'] = $member->id;
                    $data['member_no'] = $member->member_no;
                    $data['source_id'] = $post['source_id'];
                    $data['create_time'] = time();
                    $DuplicateLogModel = new DuplicateLog();
                    $DuplicateLogModel->insert($data);
                }
            }

            if ($this->auth['is_show_alias']) {
                ### 显示别名，也就是大类
                $repeatRes = $repeatPlatform;
            } else {
                ### 显示来源
                $repeatRes = $repeat;
            }

            if(!isset($post['update'])) { ## 添加客资的时候需要检测用户是否已经获取到本客资
                $allocate = MemberAllocate::getAllocate($this->user['id'], $member->id);
                if (!empty($allocate)) {
                    ### 提示用户已经拥有本客资
                    $code = '501';
                    $msg = '您已经拥有本客资，请勿重复添加';
                } else {
                    ### 分配客资到该用户
                    $memberData = $member->getData();
                    MemberAllocate::updateAllocateData($this->user['id'], $member->id, $memberData);
                    $code = '502';
                    $msg =  '此客资已经分配给您';
                }

                return json([
                    'code' => $code,
                    'msg' => $msg,
                    'result' => [
                        'repeat' => $repeatRes
                    ]
                ]);
            } else { ### 回访页面只需要 更新重复记录，不需要重新分配
                return json([
                    'code' => '501',
                    'msg' => '成功',
                    'result' => [
                        'repeat' => $repeatRes
                    ]
                ]);
            }
        } else {

            return json([
                'code'   => '200',
                'msg'    => '恭喜，该号码验证通过'
            ]);
        }
    }

    public function getDuplicateLog()
    {
        $post = Request::param();
        $title = [];
        if($this->auth['is_show_alias']) {
            $map = [];
            $map[] = ['member_id', '=', $post['member_id']];
            $list = DuplicateLog::where($map)->group('source_id')->column('source_id');
            if ($list) {
                foreach ($list as $value) {
                    if (isset($this->sources[$value])) {
                        $title[] = $this->sources[$value]['title'];
                    }
                }
            }
        } else {
            $recommenders = Recommender::column('id,title', 'id');
            $map = [];
            $map[] = ['member_id', '=', $post['member_id']];
            $list = DuplicateLog::where($map)->group('recommender_id')->column('recommender_id');
            echo DuplicateLog::where($map)->group('recommender_id')->getLastSql();
            if ($list) {
                foreach ($list as $value) {
                    if (isset($recommenders[$value])) {
                        $title[] = $recommenders[$value];
                    }
                }
            }
        }

        if(!empty($title)) {
            return json([
                'code'   => '200',
                'msg'    => '获取重复列表成功',
                'result' => [
                    'logs'   => $title
                ]
            ]);
        } else {
            return json([
                'code'  => '500',
                'msg'   => '获取重复列表失败'
            ]);
        }
    }
}