<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/7/7
 * Time: 7:13 PM
 */

namespace app\index\controller;

use think\facade\Request;

class Manager extends Base
{
    public function applyVisit()
    {
        if(Request::isAjax()) {
            $sources = \app\index\model\Source::getSources();
            $intentions = Intention::getIntentions();
            $hotels = Hotel::getHotels();
            $newsTypes = ['婚宴信息', '婚庆信息', '婚宴转婚庆'];

            $get = Request::param();
            $user = Session::get("user");
            $config = [
                'page' => $get['page']
            ];
            $map[] = ['operate_id', '=', $user['id']];
            $list = model('MemberApply')->where($map)->with('member')->paginate($get['limit'], false, $config);
            $data = $list->getCollection()->toArray();
            foreach ($data as &$value) {
                $member = $value['member'];
                unset($member['id']);
                unset($value['member']);
                $value = array_merge($value, $member);
                $value['mobile'] = substr_replace($value['mobile'],'****', 3, 4);
                $value['news_type'] = $newsTypes[$value['news_type']];
                $value['hotel_id'] = $hotels[$value['hotel_id']];
            }
            $result = [
                'code'  => 0,
                'msg'   => '获取数据成功',
                'count' => $list->total(),
                'data'  => $data
            ];
            return json($result);

        } else {
            return $this->fetch();
        }
    }

    public function applyToOrder()
    {

    }
}