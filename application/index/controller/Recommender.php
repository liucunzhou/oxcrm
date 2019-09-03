<?php
namespace app\index\controller;

use app\api\model\Member;
use app\common\model\Allocate;
use app\common\model\DuplicateLog;
use app\common\model\MemberAllocate;
use think\facade\Request;

class Recommender extends Base
{
    public function index()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];
            $list = model('recommender')->where($map)->order('is_valid desc,sort desc,id asc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection();
            foreach ($data as &$value){
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
            $this->view->engine->layout(false);
            return $this->fetch();
        }
    }

    public function addRecommender()
    {
        return $this->fetch('edit_recommender');
    }

    public function editRecommender()
    {
        $get = Request::param();
        $data = \app\common\model\Recommender::get($get['id']);
        $this->assign('data', $data);

        return $this->fetch();
    }

    public function getRecommenderList()
    {
        $list = \app\common\model\Recommender::column('id,title', id);
        return json($list);
    }

    public function doEditRecommender()
    {
        $post = Request::param();
        if(isset($post['id']) && $post['id'] > 0) {
            $action = '更新推荐人';
            $Model = \app\common\model\Recommender::get($post['id']);
        } else {
            $member = Member::getByMobile($post['mobile']);
            $recommender = \app\common\model\Recommender::getByTitle($post['title']);
            if(!empty($recommender) && isset($post['source'])) {
                $recommenderId = $recommender->id;
                if (!empty($member)) {
                    $data = $member->getData();
                    $AllocateResult = MemberAllocate::updateAllocateData($this->user['id'], $member->id, $data);
                    $sql = $AllocateResult->getLastSql();
                    if (stripos($sql, 'update') === 0) {
                        $code = '201';
                        $msg = "您已存在本条客资，请直接回访";
                    } else {
                        $code = '200';
                        $msg = "客资已分配给您，请直接在我的客资中回访";
                    }

                    ### 添加到重复列表
                    $DuplicateLog = new DuplicateLog();
                    $DuplicateLog->insert($data);
                    return json([
                        'code' => $code,
                        'id' => $recommenderId,
                        'msg' => $msg
                    ]);
                } else {
                    return json([
                        'code' => '202',
                        'id' => $recommenderId,
                        'msg' => '添加推荐人成功'
                    ]);
                }
            }

            $action = '添加推荐人';
            $Model = new \app\common\model\Recommender();
        }

        // $Model::create($post);
        $result = $Model->save($post);
        $recommenderId = $Model->id;
        if($result) {
            ### 更新缓存
            if (!empty($member) && !isset($post['id'])) {
                $data = $member->getData();
                $AllocateResult = MemberAllocate::updateAllocateData($this->user['id'], $member->id, $data);
                $sql = $AllocateResult->getLastSql();
                if (stripos($sql, 'udpate') === 0) {
                    $code = '201';
                    $msg = "您已存在本条客资，请直接回访";
                } else {
                    $code = '200';
                    $msg = "客资已分配给您，请直接在我的客资中回访";
                }

                ### 加入重复日志
                $DuplicateLog = new DuplicateLog();
                $DuplicateLog->insert($data);
                return json([
                    'code' => $code,
                    'id' => $recommenderId,
                    'msg' => $msg
                ]);
            } else {
                ### 添加操作日志
                \app\common\model\OperateLog::appendTo($Model);
                return json(['code' => '202', 'id' => $recommenderId, 'msg' => $action . '成功']);
            }
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function deleteRecommender()
    {
        $post = Request::post();
        $Model = \app\common\model\Recommender::get($post['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\common\model\Recommender::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}