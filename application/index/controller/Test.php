<?php
namespace app\index\controller;

use app\api\model\Member;
use app\common\model\Client;
use app\common\model\MemberAllocate;
use app\common\model\MemberVisit;
use app\common\model\Rong;
use app\common\model\Source;
use app\common\model\User;
use Firebase\JWT\JWT;
use think\Controller;
use think\facade\Request;

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

    public function index()
    {
        $mobileModel = new Rong();
        $mobile = "18321277411";
        $result = $mobileModel->createSeatAccount($mobile);

        echo "<pre>";
        print_r($result);
    }

    public function call()
    {
        $mobileModel = new Rong();
        $caller = '18321277411';
        $callee = '13764570091';
        $result = $mobileModel->call($caller, $callee);
        print_r($result);
    }

    public function decode()
    {
        $post = Request::param();
        if(empty($post['token'])) {
            $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MzgzLCJyb2xlX2lkIjoiOCIsImRlcGFydG1lbnRfaWQiOjMxLCJhZG1pbl9pZCI6MCwibmlja25hbWUiOiIxNTIyMTc5MjkzMiIsInJlYWxuYW1lIjoiXHU2Yzg4XHU1MTQzXHU2MDdhIiwiZGluZ2RpbmciOiIiLCJtb2JpbGUiOiIxNTIyMTc5MjkzMiIsImVtYWlsIjoiIiwic29ydCI6MCwiaXNfdmFsaWQiOjEsImRlbGV0ZV90aW1lIjowLCJtb2RpZnlfdGltZSI6IjIwMTktMDgtMjEgMTE6MDk6MDYiLCJjcmVhdGVfdGltZSI6IjIwMTktMDItMjcgMTE6NTg6NDYiLCJ1c2VyX25vIjoiY2xpZW50ZXJfZWY5YTIzYjFlZWY0NDNmMzk2YjM2MzdlZjZmOGZhMmUiLCJpbl90aW1lIjpudWxsLCJmYW1pbHlfbW9iaWxlIjpudWxsLCJpZF9jYXJkIjpudWxsLCJhdmF0YXIiOiIiLCJzZXgiOiIgMCIsIm9yaWdpbl9kZXBhcnRtZW50X2lkIjoifHN5c3RlbXJvbGVfYWY2MzA3OWQ0YTBiNDVlZTk1OTBiZDMxYTJhMDk0NTN8IiwicHJvdmluY2VfaWQiOjAsImNpdHlfaWQiOjgwMn0.ykOz6DO8aL7eYvpXqanOCaAE0Wxxu03S0mfxPaI--GU';
        } else {
            $token = $post['token'];
        }
        // $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6NTUzLCJpZHMiOiIiLCJyb2xlX2lkIjoiOCIsImRlcGFydG1lbnRfaWQiOjUyLCJhZG1pbl9pZCI6MCwibmlja25hbWUiOiIxODMyMTI3NzQxMSIsInJlYWxuYW1lIjoiXHU2ZDRiXHU4YmQ1IiwiZGluZ2RpbmciOiIwNTU1MzI0NzE5Nzk1NDM5IiwibW9iaWxlIjoiMTgzMjEyNzc0MTEiLCJlbWFpbCI6IiIsInNvcnQiOjAsImlzX3ZhbGlkIjoxLCJkZWxldGVfdGltZSI6MCwibW9kaWZ5X3RpbWUiOiIyMDE5LTA5LTE3IDE5OjA3IiwiY3JlYXRlX3RpbWUiOiIyMDE5LTA4LTA0IDE0OjMxIiwidXNlcl9ubyI6ImNsaWVudGVyXzUzNzIzNWYzOGNiMzQzNjRhM2ZmMTgwMjQwNWI3OGQ5IiwidXNlcl9ub3MiOm51bGwsImluX3RpbWUiOm51bGwsImZhbWlseV9tb2JpbGUiOm51bGwsImlkX2NhcmQiOm51bGwsImF2YXRhciI6IiIsInNleCI6IjAiLCJvcmlnaW5fZGVwYXJ0bWVudF9pZCI6InxzeXN0ZW1yb2xlX2FmNjMwNzlkNGEwYjQ1ZWU5NTkwYmQzMWEyYTA5NDUzfCIsInByb3ZpbmNlX2lkIjowLCJjaXR5X2lkIjowfQ.R9xODEmgwqdXIoudfz3q9ZLF61_8h1EwkqKs_IcwVqw';
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

    public function sysncAllocateTime()
    {
        $get = Request::param();
        $config = [
            'page' => $get['page']
        ];
        $endTime = 1567526400;
        $map[] = ['create_time', '<', $endTime];
        $allocates = MemberAllocate::where($map)->paginate(10000, false, $config);
        // echo MemberAllocate::getLastSql();
        // exit;
        foreach ($allocates as $allocate) {
            $map = [];
            $map['user_id'] = $allocate->user_id;
            $map['member_id'] = $allocate->member_id;

            $visit = MemberVisit::where($map)->order('create_time asc')->find();
            echo MemberVisit::getLastSql();
            echo "<br>";
            if($visit) {
                $data = [];
                $data['create_time'] = strtotime($visit->create_time);
                $allocate->save($data);
                echo $allocate->getLastSql();
                echo "<br>";
            }
        }
    }

    public function checkAllocateTime()
    {
        $get = Request::param();
        $config = [
            'page' => $get['page']
        ];

        $map = [];
        $map[] = ['create_time', 'between', [1567440000,1567526400]];
        // $map[] = ['create_time', '>', 1567440000];
        echo "<pre>";
        $allocates = MemberAllocate::where($map)->paginate(10000, false, $config);
        // echo MemberAllocate::getLastSql();
        foreach ($allocates as $allocate){
            $map = [];
            $map['user_id'] = $allocate->user_id;
            $map['member_id'] = $allocate->member_id;
            $visit = MemberVisit::where($map)->order('create_time asc')->find();
            if(empty($visit)) {
                echo $allocate->id.":".$allocate->member_create_time;
                echo "<br>";
                $allocate->save(['create_time'=>$allocate->member_create_time]);
                echo $allocate->getLastSql();
                echo "<br>";
            }
        }
    }
}