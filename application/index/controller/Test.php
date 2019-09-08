<?php
namespace app\index\controller;

use app\api\model\Member;
use app\common\model\Client;
use app\common\model\Source;
use app\common\model\User;
use Firebase\JWT\JWT;
use think\Controller;

class Test extends Controller
{
    protected function initialize()
    {
        /**
        $params['token'] = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjExLCJpZHMiOiI5MSwyMTEiLCJyb2xlX2lkIjoiOCIsImRlcGFydG1lbnRfaWQiOjQ4LCJhZG1pbl9pZCI6MCwibmlja25hbWUiOiIxODcyMTI3ODYyNyIsInJlYWxuYW1lIjoiXHU2ZDc3XHU0ZWQxXHU1NTEwXHU4MzYzXHU1MzRlIiwiZGluZ2RpbmciOiIyMDA1MTgzNTI2MjE5OTA3NDciLCJtb2JpbGUiOiIxMzI0ODAzMTA4OSIsImVtYWlsIjoiIiwic29ydCI6MCwiaXNfdmFsaWQiOjEsImRlbGV0ZV90aW1lIjowLCJtb2RpZnlfdGltZSI6IjIwMTktMDktMDMgMTQ6MjMiLCJjcmVhdGVfdGltZSI6IjIwMTktMDgtMDQgMTQ6MTgiLCJ1c2VyX25vIjoiY2xpZW50ZXJfMzE5ZDRlZjZjMmMxNDY1NGJlNmIyZGNlYjg1MjMyZmMiLCJ1c2VyX25vcyI6InBhcnRuZXJfYjBkYzY1MWRiMDcwNDAwYjkxYjZiOTg3ZTRiNWJkODIsY2xpZW50ZXJfMzE5ZDRlZjZjMmMxNDY1NGJlNmIyZGNlYjg1MjMyZmMiLCJpbl90aW1lIjpudWxsLCJmYW1pbHlfbW9iaWxlIjpudWxsLCJpZF9jYXJkIjpudWxsLCJhdmF0YXIiOiIiLCJzZXgiOiIwIiwib3JpZ2luX2RlcGFydG1lbnRfaWQiOiJ8c3lzdGVtcm9sZV9hZjYzMDc5ZDRhMGI0NWVlOTU5MGJkMzFhMmEwOTQ1M3wiLCJwcm92aW5jZV9pZCI6ODAxLCJjaXR5X2lkIjo4MDJ9.KlfQrXTXmDgmxu_OPLYf4BzUR6TPK7IEkIv9plOKzHA';
        if(!isset($params['token'])) {
            echo xjson([
                'code'  => '400',
                'msg'   => 'token不能为空'
            ]);
            exit;
        } else {
            $this->params = $params;
        }

        $user = JWT::decode($params['token'], 'hongsi',['HS256']);
        if(empty($user) || empty($user['id'])) {
            xjson([
                'code'   => '401',
                'msg'   => 'token解析失败'
            ]);
            exit;
        }

        print_r($user);
        exit;

        $this->user = (array)$user;
         * **/
    }

    public function decode()
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6Mzc3LCJpZHMiOiIiLCJyb2xlX2lkIjoiOCIsImRlcGFydG1lbnRfaWQiOjI4LCJhZG1pbl9pZCI6MCwibmlja25hbWUiOiIxMzkxODU2MjI3NSIsInJlYWxuYW1lIjoiXHU5ZWM0XHU0ZjUwXHU1NDFiIiwiZGluZ2RpbmciOiIxODI2MDU1MDUwNjY5MzE0IiwibW9iaWxlIjoiMTM5MTg1NjIyNzUiLCJlbWFpbCI6IiIsInNvcnQiOjAsImlzX3ZhbGlkIjoxLCJkZWxldGVfdGltZSI6MCwibW9kaWZ5X3RpbWUiOiIyMDE5LTA5LTA2IDExOjU3IiwiY3JlYXRlX3RpbWUiOiIyMDE5LTA4LTA0IDE3OjA3IiwidXNlcl9ubyI6ImNsaWVudGVyXzM5NDA0NTQ4MTU4NTQxOWZhMGZhNzcwYmQ3NmIzZmUzIiwidXNlcl9ub3MiOm51bGwsImluX3RpbWUiOm51bGwsImZhbWlseV9tb2JpbGUiOm51bGwsImlkX2NhcmQiOm51bGwsImF2YXRhciI6IiIsInNleCI6IjAiLCJvcmlnaW5fZGVwYXJ0bWVudF9pZCI6InxzeXN0ZW1yb2xlX2FmNjMwNzlkNGEwYjQ1ZWU5NTkwYmQzMWEyYTA5NDUzfCIsInByb3ZpbmNlX2lkIjo4MDEsImNpdHlfaWQiOjgwMn0.vkwiZLHtzbwqF6L7bnGoAk-k-J64RKDX-FofPQoq3Gc';
        $user = JWT::decode($token, 'hongsi', ['HS256']);
        echo "<pre>";
        print_r($user);
    }


    public function store()
    {
        echo "<pre>";
        $sources = Source::withTrashed()->field('id,title')->select()->toArray();

        $where = [];
        $where[] = ['role_id', 'in', [5,8]];
        $userIds = User::where($where)->column('id');
        $map = [];
        $map[] = ['operate_id', 'in', $userIds];
        $start = 1567612800;
        $end = 1567827600;
        $map[] = ['create_time', 'between', [$start, $end]];
        $members = Member::where($map)->select();
        echo "<table>";
        foreach ($members as $member) {
            echo "<tr>";
            echo "<td>".$member->create_time."</td><td>".$member->source_id."<td>".$sources[$member->source_id]['title']."</td>"."</td><td>".$member->source_text."</td>";
            echo "</tr>";
            $data = [];
            $data['source_id'] = $sources[$member->source_id]['id'];
            $data['source_text'] = $sources[$member->source_id]['title'];
            // $data['modify_time'] = time();
            $where = [];
            $where[] = ['id', '=', $member->id];
            $member->save($data, $where);
            // echo $member->getLastSql();
        }
        echo "</table>";
    }
}