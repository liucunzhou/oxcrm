<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/7/7
 * Time: 7:13 PM
 */

namespace app\index\controller;

use app\index\model\Allocate;
use app\index\model\Hotel;
use app\index\model\Intention;
use app\index\model\MemberApply;
use app\index\model\Source;
use app\index\model\Store;
use app\index\model\Tab;
use app\index\model\User;
use app\index\model\UserAuth;
use think\facade\Request;

class Manager extends Base
{
    protected $newsTypes = ['婚宴信息', '婚庆信息', '一站式'];
    protected $washStatus = [];
    protected $hotels = [];
    protected $sources = [];
    protected $status = [];
    protected $auth = [];

    protected function initialize()
    {
        parent::initialize();
        // 获取系统来源,酒店列表,意向状态
        $this->sources = Source::getSources();
        $this->hotels = Store::getStoreList();
        if (in_array($this->user['role_id'], [2, 7])) {
            // 洗单组回访
            $this->status = [
                ['title' => '未跟进'],
                ['title' => '有效客资'],
                ['title' => '无效客资'],
                ['title' => '跟进中']
            ];
        } else {
            $this->status = Intention::getIntentions();
        }
        $this->auth = UserAuth::getUserLogicAuth($this->user['id']);
    }

    public function applyVisit()
    {
        $get = Request::param();
        if(Request::isAjax()) {
            $users = User::getUsers();
            $hotels = Hotel::getHotels();
            $newsTypes = ['婚宴信息', '婚庆信息', '婚宴转婚庆'];
            $config = [
                'page' => $get['page']
            ];

            if (isset($get['status'])) {
                $map[] = ['apply_status', '=', $get['status']];
            }
            $list = model('MemberApply')->where($map)->with('member')->paginate($get['limit'], false, $config);
            $data = $list->getCollection()->toArray();
            foreach ($data as &$value) {
                $member = $value['member'];
                unset($member['id']);
                unset($value['member']);
                $value = array_merge($value, $member);
                $value['user_id'] =  $users[$value['user_id']]['nickname'];
                $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                $value['news_type'] = $newsTypes[$value['news_type']];
                $value['hotel_id'] = $hotels[$value['hotel_id']];
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
            $tabs = Tab::managerApply($get);
            $this->assign('tabs', $tabs);
            $this->assign('get', $get);
            return $this->fetch();
        }
    }

    // 确认回访申请
    public function confirmVisitApply()
    {
        $ids = Request::post("ids");
        $ids = explode(',', $ids);
        if (empty($ids)) {
            return json([
                'code'  => '500',
                'msg'   => '要确认的回访申请不能为空'
            ]);
        }

        $total = count($ids);
        foreach ($ids as $id) {
            $MemberApply = MemberApply::get($id);
            $applyData = $MemberApply->getData();

            $data['user_id'] = $applyData['user_id'];
            $data['member_id'] = $applyData['member_id'];
            $data['create_time'] = time();
            $result = Allocate::updateAllocate($applyData['user_id'], $applyData['member_id'], $data);
            if($result) {
                $MemberApply->apply_status = 1;
                $MemberApply->save();
            }
        }

        return json([
            'code'  => '200',
            'msg'   => '确认回访申请成功'
        ]);
    }

    // 驳回回访申请
    public function cancelVisitApply()
    {
        $ids = Request::post("ids");
        $ids = explode(',', $ids);
        if (empty($ids)) {
            return json([
                'code'  => '500',
                'msg'   => '要驳回的回访申请不能为空'
            ]);
        }

        foreach ($ids as $id) {
            $MemberApply = MemberApply::get($id);
            $data = [];
            $data['apply_status'] = 2;
            $MemberApply->save($data);
        }

        return json([
            'code'  => '200',
            'msg'   => '驳回回访申请成功'
        ]);
    }
}