<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/4/22
 * Time: 8:47 PM
 */

namespace app\index\controller;


use app\common\model\Tab;
use think\facade\Request;

class Source extends Base
{
    public function index()
    {
        if(Request::isAjax()) {
            $get = Request::param();

            ### 获取平台列表
            $platforms = \app\common\model\Source::getPlatforms();
            if(isset($get['parent_id'])) {
                if(is_numeric($get['parent_id'])) {
                    $map[] = ['parent_id', '=', $get['parent_id']];
                } else {
                    $map[] = ['parent_id', '>', 0];
                }
            } else {
                $map[] = ['parent_id', '=', 0];
            }
            $config = [
                'page' => $get['page']
            ];
            $list = model('source')->where($map)->order('parent_id asc,sort desc,id asc')->paginate($get['limit'], false, $config);
            // echo model('source')->getLastSql();
            $data = $list->getCollection();
            foreach ($data as &$value) {
                $value['parent_id'] = $platforms[$value['parent_id']]['title'];
                $value['is_valid'] = $value['is_valid'] ? '在线' : '下线';
            }

            $result = [
                'code'  => 0,
                'msg'   => '获取数据成功',
                'count' => $list->total(),
                'data'  => $data
            ];
            return json($result);
        } else {
            $get = Request::param();
            $tabs = Tab::source($get);
            $this->assign('tabs', $tabs);
            return $this->fetch();
        }
    }

    public function addSource()
    {
        $platforms = \app\common\model\Source::getPlatforms();
        $this->assign('platforms', $platforms);

        return $this->fetch('edit_source');
    }

    public function editSource()
    {
        $platforms = \app\common\model\Source::getPlatforms();
        $this->assign('platforms', $platforms);

        $get = Request::param();
        $data = \app\common\model\Source::get($get['id']);
        $this->assign('data', $data);

        return $this->fetch();
    }

    public function doEditSource()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '添加来源';
            $Model = \app\common\model\Source::get($post['id']);
        } else {
            $action = '编辑来源';
            $Model = new \app\common\model\Source();
        }

        // 缺少字段验证
        $result = $Model->save($post);

        if($result) {
            ### 更新缓存
            \app\common\model\Source::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function deleteSource()
    {
        $get = Request::param();
        $Model = \app\common\model\Source::get($get['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\common\model\Source::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}