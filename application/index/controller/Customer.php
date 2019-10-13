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
use app\common\model\Mobile;
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
    protected $allocateTypes = ['分配获取', '全号搜索', '公海申请', '自行添加'];

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

        if (!Request::isAjax()) {
            $staffes = User::getUsersInfoByDepartmentId($this->user['department_id'], false);
            $staff = [
                'id' => 'all',
                'realname' => '所有员工'
            ];

            array_unshift($staffes, $staff);
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
            if ($this->user['role_id'] == 10 || $this->user['role_id'] == 11) { // 派单组主管、客服
                // if ($this->user['role_id'] == 11) { // 派单组主管、客服
                $result = $this->getDispatchCustomerList();
            } else {
                $result = $this->getCustomerList();
            }
            return json($result);

        } else {

            $get = Request::param();
            $get['create_time'] = str_replace('+', '', $get['create_time']);
            $this->assign('get', $get);

            if ($this->user['role_id'] == 10 || $this->user['role_id'] == 11) { // 派单组主管、客服
                $tabs = Tab::dispatchMine($this->user, $get);
                $this->assign('tabs', $tabs);
                if (isset($get['sea'])) {
                    return $this->fetch('mine_dispatch_sea');
                } else {
                    return $this->fetch('mine_dispatch');
                }
            } else {
                $tabs = Tab::customerMine($this->user, $get);
                $this->assign('tabs', $tabs);
                return $this->fetch();
            }
        }
    }

    /**
     * 派单组客资列表
     */
    private function getDispatchCustomerList()
    {
        $get = Request::param();
        $config = [
            'page' => $get['page']
        ];

        $map = Search::customerDispatchMine($this->user, $get);
        isset($get['keywords']) && $get['keywords'] = trim($get['keywords']);
        if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
            $mobiles = MobileRelation::getMobiles($get['keywords']);
            if (!empty($mobiles)) {
                $map[] = ['mobile', 'in', $mobiles];
            } else {
                $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
            }
        } else if (isset($get['keywords']) && !empty($get['keywords']) && strlen($get['keywords']) < 11) {
            $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
        } else if (isset($get['keywords']) && strlen($get['keywords']) > 11) {
            $map[] = ['mobile', '=', $get['keywords']];
        }

        if ($get['sea']) {
            $users = User::getUsers();
            $map[] = ['dispatch_id', '=', 0];
            $map[] = ['add_source', '=', 1];
            $list = model('Member')->where($map)->order('create_time desc')->paginate($get['limit'], false, $config);
            if (!empty($list)) {
                $data = $list->getCollection()->toArray();
                foreach ($data as &$value) {
                    $value['get_customer_from_dispatch'] = '索取';
                    $value['operator'] = $users[$value['operate_id']]['realname'];
                    $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                    $value['news_type'] = $this->newsTypes[$value['news_type']];
                    $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                    $value['dispatch_assign_status'] = $value['dispatch_assign_status'] ? "已分配" : "未分配";

                    if ($this->auth['is_show_alias'] == '1') {
                        $value['source_text'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : $value['source_text'];
                    } else {
                        $value['source_text'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : $value['source_text'];
                    }
                }
                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'pages' => $list->lastPage(),
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
        } else {
            $list = model('MemberAllocate')->where($map)->order('create_time desc,member_create_time desc')->paginate($get['limit'], false, $config);
            if (!empty($list)) {
                $users = User::getUsers();
                $data = $list->getCollection()->toArray();
                foreach ($data as &$value) {
                    $allocateType = $value['allocate_type'];
                    $value['allocate_type'] = $this->allocateTypes[$allocateType];
                    $value['operator'] = $users[$value['operate_id']]['realname'];
                    $value['user_realname'] = $users[$value['user_id']]['realname'];
                    $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                    $value['news_type'] = $this->newsTypes[$value['news_type']];
                    $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
                    $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                    $value['allocate_time'] = $value['create_time'];
                    $value['assign_status'] = $value['assign_status'] ? '已分配': '未分配';

                    if ($value['member_id'] > 0) {
                        $memberObj = Member::get($value['member_id']);
                        $value['visit_amount'] = $memberObj->visit_amount;
                        $value['remark'] = $memberObj->remark;
                        if ($value['member_create_time'] > 0) {
                            $value['member_create_time'] = date('Y-m-d H:i', $value['member_create_time']);
                        } else {
                            $value['member_create_time'] = $memberObj->create_time;
                        }
                        if ($this->auth['is_show_alias'] == '1') {
                            $value['source_text'] = $this->sources[$memberObj->source_id] ? $this->sources[$memberObj->source_id]['title'] : $memberObj->source_text;
                        } else {
                            $value['source_text'] = $this->sources[$memberObj->source_id] ? $this->sources[$memberObj->source_id]['title'] : $memberObj->source_text;
                        }
                    }
                }

                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => $list->total(),
                    'data' => $data,
                    'map' => $map
                ];
            } else {
                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => 0,
                    'data' => []
                ];
            }
        }

        return $result;
    }

    /**
     * 除派单组外我的客资列表
     */
    private function getCustomerList()
    {
        $get = Request::param();
        $config = [
            'page' => $get['page']
        ];
        $map = Search::customerMine($this->user, $get);
        if(isset($get['keywords']) && !empty($get['keywords'])){
            $get['keywords'] = trim($get['keywords']);
            preg_match_all('/\d+/',$get['keywords'],$arr);

        }

        if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
            $mobiles = MobileRelation::getMobiles($get['keywords']);
            if (!empty($mobiles)) {
                $map[] = ['mobile', 'in', $mobiles];
            } else {
                $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
            }
        } else if (isset($get['keywords']) && !empty($get['keywords']) && strlen($get['keywords']) < 11) {
            $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
        } else if (isset($get['keywords']) && strlen($get['keywords']) > 11) {
            $map[] = ['mobile', '=', $get['keywords']];
        }

        $list = model('MemberAllocate')->where($map)->order('create_time desc,member_create_time desc')->paginate($get['limit'], false, $config);
        if (!empty($list)) {
            $users = User::getUsers();
            $data = $list->getCollection()->toArray();
            foreach ($data as &$value) {
                $allocateType = $value['allocate_type'];
                $value['allocate_type'] = $this->allocateTypes[$allocateType];
                $value['operator'] = $users[$value['operate_id']]['realname'];
                $value['user_realname'] = $users[$value['user_id']]['realname'];
                $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                $value['news_type'] = $this->newsTypes[$value['news_type']];
                $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
                $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                $value['allocate_time'] = $value['create_time'];
                $value['assign_status'] = $value['assign_status'] ? '已分配': '未分配';

                if ($value['member_id'] > 0) {
                    $memberObj = Member::get($value['member_id']);
                    $value['visit_amount'] = $this->user['role_id']!=9 ? $memberObj->visit_amount : '-';
                    $value['remark'] = $memberObj->remark;
                    if ($value['member_create_time'] > 0) {
                        $value['member_create_time'] = date('Y-m-d H:i', $value['member_create_time']);
                    } else {
                        $value['member_create_time'] = $memberObj->create_time;
                    }
                    if ($this->auth['is_show_alias'] == '1') {
                        $value['source_text'] = $this->sources[$memberObj->source_id] ? $this->sources[$memberObj->source_id]['title'] : $memberObj->source_text;
                    } else {
                        $value['source_text'] = $this->sources[$memberObj->source_id] ? $this->sources[$memberObj->source_id]['title'] : $memberObj->source_text;
                    }
                }
            }
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $data,
                'map' => $map,
                'keywords' => strlen($get['keywords'])
            ];
        } else {
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => 0,
                'data' => [],
                'keywords' => strlen($get['keywords'])
            ];
        }

        return $result;
    }

    public function wedding()
    {
        $get = Request::param();
        if (Request::isAjax()) {
            $config = [
                'page' => $get['page']
            ];

            $map = Search::customerWeddingMine($this->user, $get);
            isset($get['keywords']) && $get['keywords'] = trim($get['keywords']);
            if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
                $mobiles = MobileRelation::getMobiles($get['keywords']);
                if (!empty($mobiles)) {
                    $map[] = ['mobile', 'in', $mobiles];
                } else {
                    $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
                }
            } else if (isset($get['keywords']) && !empty($get['keywords']) && strlen($get['keywords']) < 11) {
                $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
            } else if (isset($get['keywords']) && strlen($get['keywords']) > 11) {
                $map[] = ['mobile', '=', $get['keywords']];
            }

            $list = model('MemberAllocate')->where($map)->order('create_time desc')->paginate($get['limit'], false, $config);
            if (!empty($list)) {
                $users = User::getUsers();
                $data = $list->getCollection()->toArray();
                foreach ($data as &$value) {
                    $allocateType = $value['allocate_type'];
                    $value['allocate_type'] = $this->allocateTypes[$allocateType];
                    $value['operator'] = $users[$value['operate_id']]['realname'];
                    $value['user_realname'] = $users[$value['user_id']]['realname'];
                    $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                    $value['news_type'] = $this->newsTypes[$value['news_type']];
                    $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
                    $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                    $value['allocate_time'] = $value['create_time'];
                    $value['assign_status'] = $value['assign_status'] ? '已分配' : '未分配';

                    if ($value['member_id'] > 0) {
                        $memberObj = Member::get($value['member_id']);
                        $value['visit_amount'] = $memberObj->visit_amount;
                        $value['remark'] = $memberObj->remark;
                        if ($value['member_create_time'] > 0) {
                            $value['member_create_time'] = date('Y-m-d H:i', $value['member_create_time']);
                        } else {
                            $value['member_create_time'] = $memberObj->create_time;
                        }
                        if ($this->auth['is_show_alias'] == '1') {
                            $value['source_text'] = $this->sources[$memberObj->source_id] ? $this->sources[$memberObj->source_id]['title'] : $memberObj->source_text;
                        } else {
                            $value['source_text'] = $this->sources[$memberObj->source_id] ? $this->sources[$memberObj->source_id]['title'] : $memberObj->source_text;
                        }
                    }
                }

                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => $list->total(),
                    'data' => $data,
                    'map' => $map
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
            $tabs = Tab::customerWedding($this->user, $get, 'Customer/wedding');
            $this->assign('tabs', $tabs);
            return $this->fetch();
        }
    }

    ### 洗单组
    public function wash()
    {
        $get = Request::param();
        if(Request::isAjax()) {
            $config = [
                'page' => $get['page']
            ];
            $map = Search::customerMine($this->user, $get);
            if (isset($get['keywords']) && !empty($get['keywords'])) {
                $get['keywords'] = trim($get['keywords']);
                preg_match_all('/\d+/', $get['keywords'], $arr);

            }

            if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
                $mobiles = MobileRelation::getMobiles($get['keywords']);
                if (!empty($mobiles)) {
                    $map[] = ['mobile', 'in', $mobiles];
                } else {
                    $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
                }
            } else if (isset($get['keywords']) && !empty($get['keywords']) && strlen($get['keywords']) < 11) {
                $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
            } else if (isset($get['keywords']) && strlen($get['keywords']) > 11) {
                $map[] = ['mobile', '=', $get['keywords']];
            }

            if(isset($get['status']) && $get['status'] == 0) {
                $order = 'create_time asc';
            } else {
                $order = 'create_time desc';
            }

            $list = model('MemberAllocate')->where($map)->order($order)->paginate($get['limit'], false, $config);
            if (!empty($list)) {
                $users = User::getUsers();
                $data = $list->getCollection()->toArray();
                foreach ($data as &$value) {
                    $allocateType = $value['allocate_type'];
                    $value['allocate_type'] = $this->allocateTypes[$allocateType];
                    $value['operator'] = $users[$value['operate_id']]['realname'];
                    $value['user_realname'] = $users[$value['user_id']]['realname'];
                    $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                    $value['news_type'] = $this->newsTypes[$value['news_type']];
                    $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
                    $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                    $value['allocate_time'] = $value['create_time'];
                    $value['assign_status'] = $value['assign_status'] ? '已分配' : '未分配';

                    if ($value['member_id'] > 0) {
                        $memberObj = Member::get($value['member_id']);
                        $value['visit_amount'] = $memberObj->visit_amount;
                        $value['remark'] = $memberObj->remark;
                        if ($value['member_create_time'] > 0) {
                            $value['member_create_time'] = date('Y-m-d H:i', $value['member_create_time']);
                        } else {
                            $value['member_create_time'] = $memberObj->create_time;
                        }
                        if ($this->auth['is_show_alias'] == '1') {
                            $value['source_text'] = $this->sources[$memberObj->source_id] ? $this->sources[$memberObj->source_id]['title'] : $memberObj->source_text;
                        } else {
                            $value['source_text'] = $this->sources[$memberObj->source_id] ? $this->sources[$memberObj->source_id]['title'] : $memberObj->source_text;
                        }
                    }
                }
                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => $list->total(),
                    'data' => $data,
                    'map' => $map,
                    'keywords' => strlen($get['keywords'])
                ];
            } else {
                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => 0,
                    'data' => [],
                    'keywords' => strlen($get['keywords'])
                ];
            }

            return json($result);

        } else {

            $get['create_time'] = str_replace('+', '', $get['create_time']);
            $this->assign('get', $get);
            $tabs = Tab::customerMine($this->user, $get, 'Customer/wash');
            $this->assign('tabs', $tabs);
            return $this->fetch('mine');
        }
    }

    ###  推荐组
    public function recommend()
    {
        $get = Request::param();
        if (Request::isAjax()) {
            $config = [
                'page' => $get['page']
            ];
            $map = Search::customerMine($this->user, $get);
            if (isset($get['keywords']) && !empty($get['keywords'])) {
                $get['keywords'] = trim($get['keywords']);
                preg_match_all('/\d+/', $get['keywords'], $arr);

            }

            if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
                $mobiles = MobileRelation::getMobiles($get['keywords']);
                if (!empty($mobiles)) {
                    $map[] = ['mobile', 'in', $mobiles];
                } else {
                    $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
                }
            } else if (isset($get['keywords']) && !empty($get['keywords']) && strlen($get['keywords']) < 11) {
                $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
            } else if (isset($get['keywords']) && strlen($get['keywords']) > 11) {
                $map[] = ['mobile', '=', $get['keywords']];
            }

            $list = model('MemberAllocate')->where($map)->order('create_time desc,member_create_time desc')->paginate($get['limit'], false, $config);
            if (!empty($list)) {
                $users = User::getUsers();
                $data = $list->getCollection()->toArray();
                foreach ($data as &$value) {
                    $allocateType = $value['allocate_type'];
                    $value['allocate_type'] = $this->allocateTypes[$allocateType];
                    $value['operator'] = $users[$value['operate_id']]['realname'];
                    $value['user_realname'] = $users[$value['user_id']]['realname'];
                    $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                    $value['news_type'] = $this->newsTypes[$value['news_type']];
                    $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
                    $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                    $value['allocate_time'] = $value['create_time'];
                    $value['assign_status'] = $value['assign_status'] ? '已分配' : '未分配';

                    if ($value['member_id'] > 0) {
                        $memberObj = Member::get($value['member_id']);
                        $value['visit_amount'] = $memberObj->visit_amount;
                        $value['remark'] = $memberObj->remark;
                        if ($value['member_create_time'] > 0) {
                            $value['member_create_time'] = date('Y-m-d H:i', $value['member_create_time']);
                        } else {
                            $value['member_create_time'] = $memberObj->create_time;
                        }
                        if ($this->auth['is_show_alias'] == '1') {
                            $value['source_text'] = $this->sources[$memberObj->source_id] ? $this->sources[$memberObj->source_id]['title'] : $memberObj->source_text;
                        } else {
                            $value['source_text'] = $this->sources[$memberObj->source_id] ? $this->sources[$memberObj->source_id]['title'] : $memberObj->source_text;
                        }
                    }
                }
                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => $list->total(),
                    'data' => $data,
                    'map' => $map,
                    'keywords' => strlen($get['keywords'])
                ];
            } else {
                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => 0,
                    'data' => [],
                    'keywords' => strlen($get['keywords'])
                ];
            }

            return json($result);
        } else {

            $get['create_time'] = str_replace('+', '', $get['create_time']);
            $this->assign('get', $get);
            $tabs = Tab::customerMine($this->user, $get, 'Customer/recommend');
            $this->assign('tabs', $tabs);
            return $this->fetch();
        }
    }


    ###  商务组
    public function merchant()
    {
        $get = Request::param();
        if (Request::isAjax()) {
            $config = [
                'page' => $get['page']
            ];
            $map = Search::customerMine($this->user, $get);
            if (isset($get['keywords']) && !empty($get['keywords'])) {
                $get['keywords'] = trim($get['keywords']);
                preg_match_all('/\d+/', $get['keywords'], $arr);

            }

            if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
                $mobiles = MobileRelation::getMobiles($get['keywords']);
                if (!empty($mobiles)) {
                    $map[] = ['mobile', 'in', $mobiles];
                } else {
                    $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
                }
            } else if (isset($get['keywords']) && !empty($get['keywords']) && strlen($get['keywords']) < 11) {
                $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
            } else if (isset($get['keywords']) && strlen($get['keywords']) > 11) {
                $map[] = ['mobile', '=', $get['keywords']];
            }

            $list = model('MemberAllocate')->where($map)->order('create_time desc,member_create_time desc')->paginate($get['limit'], false, $config);
            if (!empty($list)) {
                $users = User::getUsers();
                $data = $list->getCollection()->toArray();
                foreach ($data as &$value) {
                    $allocateType = $value['allocate_type'];
                    $value['allocate_type'] = $this->allocateTypes[$allocateType];
                    $value['operator'] = $users[$value['operate_id']]['realname'];
                    $value['user_realname'] = $users[$value['user_id']]['realname'];
                    $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                    $value['news_type'] = $this->newsTypes[$value['news_type']];
                    $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
                    $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                    $value['allocate_time'] = $value['create_time'];
                    $value['assign_status'] = $value['assign_status'] ? '已分配' : '未分配';

                    if ($value['member_id'] > 0) {
                        $memberObj = Member::get($value['member_id']);
                        $value['visit_amount'] = '-';
                        $value['remark'] = $memberObj->remark;
                        if ($value['member_create_time'] > 0) {
                            $value['member_create_time'] = date('Y-m-d H:i', $value['member_create_time']);
                        } else {
                            $value['member_create_time'] = $memberObj->create_time;
                        }
                    }
                }

                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => $list->total(),
                    'data' => $data,
                    'map' => $map,
                    'keywords' => strlen($get['keywords'])
                ];
            } else {
                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => 0,
                    'data' => [],
                    'keywords' => strlen($get['keywords'])
                ];
            }

            return json($result);
        } else {

            $get['create_time'] = str_replace('+', '', $get['create_time']);
            $this->assign('get', $get);
            $tabs = Tab::customerMine($this->user, $get, 'Customer/recommend');
            $this->assign('tabs', $tabs);
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
            if (isset($get['source']) && $get['source'] > 0) {
                $map[] = ['source_id', '=', $get['source']];
            }

            if (isset($get['staff']) && $get['staff'] > 0) {
                $map[] = ['operate_id', '=', $get['staff']];
            }
            ###  默认隐藏失效、无效客资
            $map[] = ['active_status', 'not in', [3, 4]];
            if (isset($get['date_range']) && !empty($get['date_range'])) {
                $range = explode('~', $get['date_range']);
                $range[0] = trim($range[0]);
                $range[1] = trim($range[1]);
                $start = strtotime($range[0]);
                $end = strtotime($range[1]);
                $map[] = ['create_time', 'between', [$start, $end]];
            }

            ### 省市划分
            if ($this->user['city_id'] > 0) {
                $map[] = ['city_id', '=', $this->user['city_id']];
            }

            if (isset($get['keywords']) && strlen($get['keywords']) == 11) {

                $map = [];
                $mobiles = MobileRelation::getMobiles($get['keywords']);
                if (!empty($mobiles)) {
                    $map[] = ['mobile', 'in', $mobiles];
                } else {
                    $map[] = ['mobile', '=', $get['keywords']];
                }
                $where1[] = ['mobile', 'like', "%{$get['keywords']}%"];
                $where2[] = ['mobile1', 'like', "%{$get['keywords']}%"];
                $list = model('Member')->where($map)->whereOr($where1)->whereOr($where2)->order('create_time desc')->paginate($get['limit'], false, $config);

            } else if (isset($get['keywords']) && !empty($get['keywords']) && strlen($get['keywords']) < 11) {

                $map = [];
                $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
                $list = model('Member')->where($map)->order('create_time desc')->paginate($get['limit'], false, $config);

            } else if (isset($get['keywords']) && strlen($get['keywords']) > 11) {

                $map = [];
                $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
                $list = model('Member')->where($map)->order('create_time desc')->paginate($get['limit'], false, $config);

            } else {

                $list = model('Member')->where($map)->where('id', 'not in', function ($query) {
                    $map = [];
                    $map[] = ['user_id', '=', $this->user['id']];
                    $query->table('tk_member_allocate')->where($map)->field('member_id');
                })->order('create_time desc')->paginate($get['limit'], false, $config);

            }
            $data = $list->getCollection()->toArray();
            $users = User::getUsers();
            foreach ($data as &$value) {
                $value['operator'] = $users[$value['operate_id']]['realname'];
                $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                $value['news_type'] = $this->newsTypes[$value['news_type']];
                $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";

                if ($this->auth['is_show_alias'] == '1') {
                    $value['source_text'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : $value['source_text'];
                } else {
                    $value['source_text'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : $value['source_text'];
                }
            }
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'pages' => $list->lastPage(),
                'count' => $list->total(),
                'map'   => $map,
                'data' => $data
            ];

            return json($result);

        } else {

            if(strlen($get['keywords']) == 11) {
                $this->assign('showGetEntireBtn', 1);
            } else {
                $this->assign('showGetEntireBtn', 2);
            }

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

            if (!isset($get['next_visit_time']) || empty($get['next_visit_time'])) {
                $tomorrow = strtotime('tomorrow');
                $map[] = ['next_visit_time', '>', 0];
                $map[] = ['next_visit_time', '<', $tomorrow];
            }

            $map[] = ['active_status', 'not in', [2, 3, 4]];

            if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
                $map = [];
                $mobiles = MobileRelation::getMobiles($get['keywords']);
                if (!empty($mobiles)) {
                    $map[] = ['mobile', 'in', $mobiles];
                } else {
                    $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
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

            if (!empty($list)) {
                $users = User::getUsers();
                $data = $list->getCollection()->toArray();
                if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
                    if (isset($data[0]) && !empty($data[0])) {
                        $data[0]['active_status'] = 0;
                        MemberAllocate::updateAllocateData($this->user['id'], $data[0]['member_id'], $data[0]);
                    }
                }

                foreach ($data as &$value) {
                    $value['next_visit_time'] = date('Y-m-d H:i', $value['next_visit_time']);
                    $value['operator'] = $users[$value['operate_id']]['realname'];
                    $value['user_realname'] = $users[$value['user_id']]['realname'];
                    $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                    $value['news_type'] = $this->newsTypes[$value['news_type']];
                    $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
                    $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                    if ($this->auth['is_show_alias'] == '1') {
                        $value['source_text'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : $value['source_text'];
                    } else {
                        $value['source_text'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : $value['source_text'];
                    }
                    $value['allocate_time'] = $value['create_time'];
                    $value['member_create_time'] = date('Y-m-d H:i', $value['member_create_time']);

                    if ($value['member_id'] > 0) {
                        $memberObj = Member::get($value['member_id']);
                        $value['visit_amount'] = $this->user['role_id']!=9 ? $memberObj->visit_amount : '-';
                    }
                }
                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => $list->total(),
                    'data' => $data,
                    'map'   => $map
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
            if($this->user['role_id'] == 9) {
                $view = 'today_merchant';
            } else {
                $view = 'today';
            }

            return $this->fetch($view);
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
        if ($this->user['city_id']) {
            $areas = Region::getAreaList($this->user['city_id']);
        }
        $this->assign('areas', $areas);

        $data['area_id'] = [];
        $this->assign("data", $data);

        $action = 'add';
        $this->assign('action', $action);

        return $this->fetch();
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
        if (!empty($this->user['city_id'])) {
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

            $post['mobile'] = trim($post['mobile']);
            $len = strlen($post['mobile']);
            if ($len != 11) {
                return xjson([
                    'code' => '400',
                    'msg' => '请输入正确的手机号',
                ]);
            }

            $post['mobile1'] = trim($post['mobile1']);
            if (!empty($post['mobile1']) && $len != 11) {
                return xjson([
                    'code' => '401',
                    'msg' => '请输入正确的其他手机号',
                ]);
            }

            $action = '添加客资';
            $Model = new \app\common\model\Member();
            $Model->member_no = date('YmdHis') . rand(100, 999);
            ### 验证手机号唯一性
            if(empty($post['mobile1'])) {
                $originMember = $Model::checkFromMobileSet($post['mobile'], false);
                if ($originMember) {
                    return xjson([
                        'code' => '400',
                        'msg' => $post['mobile'] . '手机号已经存在',
                    ]);
                }
            } else {
                $originMember1 = $Model::checkFromMobileSet($post['mobile'], false);
                $originMember2 = $Model::checkFromMobileSet($post['mobile1'], false);
                if ($originMember1 || $originMember2) {
                    return xjson([
                        'code' => '400',
                        'msg' => $post['mobile'] . '手机号已经存在',
                    ]);
                }
            }

            $Model->operate_id = $this->user['id'];
            if(in_array($this->user['role_id'], [5,6,8,26])) {
                $post['add_source'] = 1; // 代表来源登陆手机端，会进入派单组公海
            }
        }
        ### 同步来源名称
        if (isset($post['source_id']) && $post['source_id'] > 0) {
            $Model->source_text = $this->sources[$post['source_id']]['title'];
        }
        ### 基本信息入库
        $Model->is_sea = 1;
        $result1 = $Model->save($post);
        ### 新添加客资要加入到分配列表中
        if (empty($post['id'])) {
            $post['allocate_type'] = 3;
            $post['operate_id'] = $this->user['id'];
            MemberAllocate::insertAllocateData($this->user['id'], $Model->id, $post);
        }

        if ($result1) {
            ### 将手机号添加到手机号库
            if(empty($post['id'])) {
                $memberId = $Model->id;
            } else {
                $memberId = $post['id'];
            }
            $mobileModel = new Mobile();
            $mobileModel->insert(['mobile'=>$post['mobile'],'member_id'=>$memberId]);

            ### 将手机号1添加到手机号库
            if(!empty($post['mobile1'])) {
                $mobileModel->insert(['mobile'=>$post['mobile1'], 'member_id'=>$memberId]);
            }

            ### 添加操作记录
            OperateLog::appendTo($Model);
            if (isset($Allocate)) OperateLog::appendTo($Allocate);
            return json(['code' => '200', 'msg' => $action . '成功']);
        } else {
            return json(['code' => '500', 'msg' => $action . '失败, 请重试']);
        }
    }

    ### 编辑客资
    public function doSaveCustomer()
    {
        $post = Request::post();
        if (empty($post['id'])) {
            return json([
                'code'  => '500',
                'msg'   => '参数错误'
            ]);
        }

        $action = '编辑客资';
        $Model = \app\common\model\Member::get($post['id']);
        ### 同步来源名称
        if ($post['source_id'] != $Model->source_id) {
            $Model->source_text = $this->sources[$post['source_id']]['title'];
            ### 同步用户来源到已经分配出去的用户里
            $map = [];
            $map[] = ['member_id', '=', $post['id']];
            $allocate = new MemberAllocate();
            $data['source_id'] = $post['id'];
            $data['source_text'] = $this->sources[$post['source_id']]['title'];
            $allocate->save($data, $map);
        }

        ### 基本信息入库
        $Model->is_sea = 1;
        $result1 = $Model->save($post);

        if ($result1) {
            ### 添加操作记录
            OperateLog::appendTo($Model);
            if (isset($Allocate)) OperateLog::appendTo($Allocate);
            return json(['code' => '200', 'msg' => $action . '成功']);
        } else {
            return json(['code' => '500', 'msg' => $action . '失败, 请重试']);
        }
    }

    ### 绑定客户信息
    public function bindCustomer()
    {
        if (Request::isPost()) {
            $post = Request::param();
            if (empty($post['mobiles'])) {
                return json([
                    'code' => '500',
                    'msg' => '请填写要绑定的手机号,至少两个'
                ]);
            }

            $mobiles = explode(',', $post['mobiles']);
            if (count($mobiles) < 1) {
                return json([
                    'code' => '500',
                    'msg' => '请填写要绑定的手机号,至少两个'
                ]);
            }

            ### 检测是否已经有绑定的手机号
            $hadMobiles = MobileRelation::getMobiles($post['target']);
            if (!empty($hadMobiles)) {
                $mobiles = array_merge($mobiles, $hadMobiles);
            } else {
                array_push($mobiles, $post['target']);
            }

            $mobilesStr = implode(',', $mobiles);
            $total = count($mobiles);
            $success = 0;
            foreach ($mobiles as $key => $mobile) {
                $data = [];
                $data['mobile'] = $mobile;
                $data['mobiles'] = $mobilesStr;
                $MobileRelation = new MobileRelation();
                $origin = $MobileRelation->where(['mobile' => $mobile])->find();
                if (empty($origin)) {
                    $data['create_time'] = time();
                    $result = $MobileRelation->save($data);
                } else {
                    $result = $MobileRelation->save($data, ['mobile' => $mobile]);
                }

                if ($result) {
                    $success = $success + 1;
                }
            }


            if ($total == $success) {
                return json([
                    'code' => '200',
                    'msg' => '绑定号码成功'
                ]);
            } else {
                $failed = $total - $success;
                return json([
                    'code' => '500',
                    'msg' => '绑定成功{$success}个，失败{$failed}个'
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
        $result = MemberAllocate::get($get['id'])->delete();
        if ($result) {
            // 更新缓存
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
                if (!is_array($member) || empty($member)) continue;
                unset($member['id']);
                unset($value['member']);
                $value = array_merge($value, $member);
                $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                $value['news_type'] = $this->newsTypes[$value['news_type']];
                $value['hotel_id'] = $this->hotels[$value['hotel_id']]['title'];
                if ($this->auth['is_show_alias'] == '1') {
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
            if ($apply) continue;

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
        if (!isset($post['mobile'])) {
            return json([
                'code' => '500',
                'msg' => '请输入手机号'
            ]);
        }

        ### 手机号验证
        $len = strlen($post['mobile']);
        if ($len != 11) {
            return json([
                'code' => '501',
                'msg' => '请输入正确的手机号'
            ]);
        }

        ###  根据手机号获取用户信息
        $member = Member::getByMobile($post['mobile']);
        if (!empty($member)) {
            $isWriteDuplicate = false;
            $repeat = $this->sources[$post['source_id']]['title'];
            if (isset($this->sources[$post['source_id']]['parent_id'])) {
                $platformId = $this->sources[$post['source_id']]['parent_id'];
                $repeatPlatform = $this->sources[$platformId]['title'];
            } else {
                $repeatPlatform = '';
            }

            if (empty($member->repeat_log)) {
                $isWriteDuplicate = true;
            } else {
                ### 检测是否已存在title
                if (!empty($member->repeat_log) && !empty($repeat) && mb_strpos($member->repeat_log, $repeat) === false) {
                    $repeat = $member->repeat_log . ',' . $repeat;
                    $isWriteDuplicate = true;

                    if (!empty($member->repeat_platform_log) && !empty($repeatPlatform) && mb_strpos($member->repeat_platform_log, $repeatPlatform) === false) {
                        $repeatPlatform = $member->repeat_platform_log . ',' . $repeatPlatform;
                    }
                }
            }

            if ($isWriteDuplicate) {
                $member->save(['repeat_log' => $repeat, 'repeat_platform_log' => $repeatPlatform]);
                $map = [];
                $map[] = ['user_id', '=', $this->user['id']];
                $map[] = ['member_id', '=', $member->id];
                $map[] = ['source_id', '=', $post['source_id']];
                $repeatLogData = DuplicateLog::where($map)->find();

                if (empty($repeatLogData)) {
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

            if (!isset($post['update'])) { ## 添加客资的时候需要检测用户是否已经获取到本客资
                $allocate = MemberAllocate::getAllocate($this->user['id'], $member->id);
                if (!empty($allocate)) {
                    ### 提示用户已经拥有本客资
                    $code = '501';
                    $msg = '您已经拥有本客资，请勿重复添加';
                } else {
                    ### 分配客资到该用户
                    $memberData = $member->getData();
                    MemberAllocate::insertAllocateData($this->user['id'], $member->id, $memberData);
                    $code = '502';
                    $msg = '此客资已经分配给您';
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
                'code' => '200',
                'msg' => '恭喜，该号码验证通过'
            ]);
        }
    }

    /**
     * 追加来源
     * @return \think\response\Json
     */
    public function appendSource()
    {
        if (Request::isPost()) {
            $post = Request::post();
            ###  根据手机号获取用户信息
            $member = Member::get($post['id']);
            $isWriteDuplicate = false;
            $repeat = $this->sources[$post['source_id']]['title'];
            if (isset($this->sources[$post['source_id']]['parent_id'])) {
                $platformId = $this->sources[$post['source_id']]['parent_id'];
                $repeatPlatform = $this->sources[$platformId]['title'];
            } else {
                $repeatPlatform = '';
            }

            if (empty($member->repeat_log)) {
                $isWriteDuplicate = true;
            } else {
                ### 检测是否已存在title
                if (!empty($member->repeat_log) && !empty($repeat) && mb_strpos($member->repeat_log, $repeat) === false) {
                    $repeat = $member->repeat_log . ',' . $repeat;
                    $isWriteDuplicate = true;

                    if (!empty($member->repeat_platform_log) && !empty($repeatPlatform) && mb_strpos($member->repeat_platform_log, $repeatPlatform) === false) {
                        $repeatPlatform = $member->repeat_platform_log . ',' . $repeatPlatform;
                    }
                }
            }

            if ($isWriteDuplicate) {
                $member->save(['repeat_log' => $repeat, 'repeat_platform_log' => $repeatPlatform]);
                $map = [];
                $map[] = ['user_id', '=', $this->user['id']];
                $map[] = ['member_id', '=', $member->id];
                $map[] = ['source_id', '=', $post['source_id']];
                $repeatLogData = DuplicateLog::where($map)->find();

                if (empty($repeatLogData)) {
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

            return json([
                'code' => '200',
                'msg' => '追加来源成功'
            ]);
        } else {

            $this->assign('sources', $this->sources);
            return $this->fetch();
        }
    }

    public function getDuplicateLog()
    {
        $post = Request::param();
        $title = [];
        if ($this->auth['is_show_alias']) {
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

        if (!empty($title)) {
            return json([
                'code' => '200',
                'msg' => '获取重复列表成功',
                'result' => [
                    'logs' => $title
                ]
            ]);
        } else {
            return json([
                'code' => '500',
                'msg' => '获取重复列表失败'
            ]);
        }
    }

    public function getCustomerFromDispatch()
    {
        $result = 0;
        $get = Request::param();
        $member = Member::get($get['id']);
        if ($member) {
            $data = $member->getData();
            $data['active_status'] = 1;
            $result = MemberAllocate::insertAllocateData($this->user['id'], $get['id'], $data);
        }

        if ($result) {
            $member->save(['dispatch_id' => $this->user['id']]);
            return json([
                'code' => '200',
                'msg' => '索取客资成功'
            ]);
        } else {
            return json([
                'code' => '500',
                'msg' => '索取客资失败'
            ]);
        }

    }
}