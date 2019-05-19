<?php
namespace app\api\controller;
use think\Controller;

/**
 * Class News
 * @package app\api\controller
 * 发布的所有信息都认为是信息
 * 都试做是信息的一种
 */
class News extends Controller
{
    /**
     * 我们是做自己是一个新闻发布平台
     * 所以，使用issue
     * 发布的信息包括
     * 信息（活动）名称
     * 封面图
     * 时间周期 开始时间结束时间
     * 活动地点
     * 详情
     * 联系电话
     */
    public function issue()
    {
        $post = input("post.");

        $News = new \app\api\model\News();
        // $News::create($post);
        $result = $News->save($post);

        if($result) {
            return json(['code'=>'200', 'msg'=>'发布信息成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'发布信息失败']);
        }
    }
}