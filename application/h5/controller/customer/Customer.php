<?php

namespace app\h5\controller\customer;

use app\common\model\Budget;
use app\common\model\Intention;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\Source;
use app\common\model\Store;
use app\common\model\User;
use app\h5\controller\Base;
use think\facade\Request;
use app\common\model\Region;
use app\common\model\MemberVisit;
use app\common\model\MobileRelation;
use app\common\model\Search;

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
    protected $model = null;

    protected function initialize()
    {
        parent::initialize();
        // 获取系统来源,酒店列表,意向状态
        $this->sources = Source::getSources();
        $this->hotels = Store::getStoreList();
        $this->status = Intention::getIntentions();
        // $this->auth = UserAuth::getUserLogicAuth($this->user['id']);

        $this->model = new MemberAllocate();
        $this->Membermodel = new Member();
    }


    /**
     * 客资公海
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        $get = Request::param();
        $get['limit'] = isset($get['limit']) ? $get['limit'] : 3;
        $get['page'] = isset($get['page']) ? $get['page'] + 1 : 1;
        $config = [
            'page' => $get['page']
        ];

        $map = [];
        $list = $this->model->where($map)->order('create_time desc,member_create_time desc')->paginate($get['limit'], false, $config);
        if (!empty($list)) {
            $users = User::getUsers();
            $data = $list->getCollection()->toArray();

            foreach ($data as &$value) {
                $value['operator'] = $users[$value['operate_id']]['realname'];
                $value['user_realname'] = $users[$value['user_id']]['realname'];
                $value['mobile'] = substr_replace($value['mobile'], '***', 3, 3);
                $value['news_type'] = $this->newsTypes[$value['news_type']];
                $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
                $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                $value['color'] = $value['active_status'] ? $this->status[$value['active_status']]['color'] : '#FF0000';
                $value['allocate_time'] = $value['create_time'];
                if ($value['member_create_time'] > 0) {
                    $value['member_create_time'] = date('Y-m-d H:i', $value['member_create_time']);
                } else {
                    $value['member_create_time'] = $value['create_time'];
                }

                if ($value['member_id'] > 0) {
                    $memberObj = Member::get($value['member_id']);
                    $value['visit_amount'] = $memberObj->visit_amount;
                }
            }

            $result = [
                'code' => 200,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $data,
            ];
        } else {

            $result = [
                'code' => 200,
                'msg' => '获取数据成功',
                'data' => []
            ];
        }

        return xjson($result);
    }

    /**
     * 今日跟进
     * [today description]
     * @return [type] [description]
     */
    public function today()
    {
        $get = Request::param();
        $get['limit'] = isset($get['limit']) ? $get['limit'] : 3;
        $get['page'] = isset($get['page']) ? $get['page'] + 1 : 1;
        $config = [
            'page' => $get['page']
        ];

        $map[] = ['active_status', 'not in', [2, 3, 4]];
        $tomorrow = strtotime('tomorrow');
        if (!isset($get['next_visit_time']) || empty($get['next_visit_time'])) {
            $map[] = ['next_visit_time', '>', 0];
            $map[] = ['next_visit_time', 'between', [0, $tomorrow]];
        } else {
            $get['next_visit_time'] = strtotime($get['next_visit_time']);
            $map[] = ['next_visit_time', 'between', [$get['next_visit_time'], $tomorrow]];
        }

        $field = 'id,realname,mobile,active_status,member_id,create_time';
        $list = $this->model->where($map)->field($field)->order('next_visit_time desc')->paginate($get['limit'], false, $config);
        if (!empty($list)) {
            foreach ($list as &$value) {
                $value['color'] = $value['active_status'] ? $this->status[$value['active_status']]['color'] : '#FF0000';
                $value['mobile'] = substr_replace($value['mobile'], '***', 3, 3);;
                $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
            }
            $result = [
                'code' => 200,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $list->getCollection(),
            ];

        } else {

            $result = [
                'code' => 200,
                'msg' => '获取数据成功',
                'count' => 0,
                'data' => []
            ];
        }
        return json($result);
    }

    /**
     * 客资详情
     * Method member
     * @return \think\response\Json
     *
     */
    public function member()
    {
        $get = Request::param();
        $allocate = MemberAllocate::get($get['id']);
        ### 获取用户基本信息
        $field = "id,realname,mobile,mobile1,active_status,budget,banquet_size,banquet_size_end,zone,source_text,wedding_date,hotel_text,remark";
        $customer = Member::field($field)->get($allocate->member_id);
        $customer['next_visit_time'] = $allocate->next_visit_time;
        $customer['color'] = $customer['active_status'] ? $this->status[$customer['active_status']]['color'] : '#FF0000';
        $customer['active_status'] = $customer['active_status'] ? $this->status[$customer['active_status']]['title'] : "未跟进";
        if (!($this->auth['is_show_entire_mobile'] || !empty($allocate))) {
            $customer['mobile'] = substr_replace($customer['mobile'], '****', 3, 4);
            $customer['mobile1'] = substr_replace($customer['mobile1'], '****', 3, 4);
        }
        if ($customer['operate_id'] > 0) {
            $users = User::getUsers();
            $customer['operate_id'] = $users[$customer['operate_id']]['realname'];
        }

        $result = [
            'code' => 200,
            'msg' => '客资详情',
            'data' =>$customer
        ];
        return json($result);
    }

    public function mine()
    {
        $get = Request::param();
        $get['limit'] = isset($get['limit']) ? $get['limit'] : 3;
        $get['page'] = isset($get['page']) ? $get['page'] + 1 : 1;
        $config = [
            'page' => $get['page']
        ];
        $map = [];
        $list = model('MemberAllocate')->where($map)->order('create_time desc,member_create_time desc')->paginate($get['limit'], false, $config);
        return json($list->getCollection());

    }
    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
