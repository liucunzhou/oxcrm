<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/9/17
 * Time: 6:48 PM
 */

namespace app\common\command;

use app\common\model\Department;
use app\common\model\MemberAllocate;
use app\common\model\User;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Notice extends Command
{
    protected function configure()
    {
        $this->setName("Notice")
            ->addOption('action', null, Option::VALUE_REQUIRED, '要执行的动作')
            ->addOption('page', null, Option::VALUE_OPTIONAL, '分页');
    }

    protected function execute(Input $input, Output $output)
    {
        $action = '';
        if ($input->hasOption("action")) {
            $action = $input->getOption("action");
        } else {
            $output->writeln("请输入要执行的操作");
            return false;
        }

        if ($input->hasOption('page')) {
            $page = $input->getOption("page");
        } else {
            $page = 0;
        }

        switch ($action) {
            // 门店未回访提醒
            case 'noticeStoretNovist':
                $this->noticeStoretNovist();
                break;

        }
    }

    public function noticeStoretNovist()
    {
        $map = [];
        // $map[] = ['id', '=', 553];
        $map[] = ['role_id', 'in', [5,8]];
        $staffObj = User::where($map)->all();
        $staffes = $staffObj->toArray();
        if(empty($staffes)) return false;

        // $rang = 10;
        $time = time();
        $staffIds = array_column($staffes, 'id');
        $map = [];
        $map[] = ['active_status', '=', 0];
        $map[] = ['user_id', 'in', $staffIds];
        $allocates = MemberAllocate::where($map)->select();
        $DingModel = new \app\api\model\DingTalk();
        foreach ($allocates as $allocate) {
            // 后台用户信息
            $user = User::get($allocate->user_id);
            print_r($user);
            continue;
            // 获取部门信息
            $department = Department::get($user['department_id']);
            $pathArr = explode("-", $department['path']);
            array_shift($pathArr);
            array_pop($pathArr);

            // 客户信息
            $mobileLast = substr($allocate->mobile, 0, -5);

            // 推送时间计算
            $createTime = strtotime($allocate->create_time);
            $diff = $time - $createTime - 7200 * ($allocate->notice_time + 1);
            $times = $allocate->notice_time + 1;
            if($diff > 0 && $allocate->notice_time == 0) {

                if(!empty($user['dingding'])) {
                    $users[] = $user['dingding'];
                    $title = "[".date('Y-m-d')."]".'未跟进客资提醒';
                    $content = "有一条客资手机尾号为{$mobileLast}未跟进\n";
                    $content .= "提醒第{$times}次";
                    $url = "http://h5.hongsizg.com/pages/customer/mine?status=0&is_into_store=0&page_title=%E6%9C%AA%E8%B7%9F%E8%BF%9B%E5%AE%A2%E8%B5%84&time=".time();
                    $message = $DingModel->linkMessage($title, $content, $url);
                    $DingModel->sendJobMessage($users, $message);
                }
                $allocate->notice_time = ['inc', 1];
                $allocate->save();

            } else if($diff > 0 && $allocate->notice_time == 1) {
                $map = [];
                $map[] = ['role_id', '=', 5];
                $map[] = ['department_id', 'in', $pathArr];
                $ids = User::where($map)->column('dingding');
                if(!empty($ids)) {
                    // 第二次提醒，主管
                    $content = $user['realname'] . '有一条手机尾号为' . $mobileLast . "的客资未跟进\n";
                    $content .= '已经提醒2次';
                    $message = $DingModel->textMessage($content);
                    $DingModel->sendJobMessage($ids, $message);
                }

                if(!empty($user['dingding'])) {
                    $users[] = $user['dingding'];
                    $title = "[".date('Y-m-d')."]".'未跟进客资提醒';
                    $content = "有一条客资手机尾号为{$mobileLast}未跟进\n";
                    $content .= "提醒第{$times}次";
                    $url = "http://h5.hongsizg.com/pages/customer/mine?status=0&is_into_store=0&page_title=%E6%9C%AA%E8%B7%9F%E8%BF%9B%E5%AE%A2%E8%B5%84&time=".time();
                    $message = $DingModel->linkMessage($title, $content, $url);
                    $DingModel->sendJobMessage($users, $message);
                }
                $allocate->notice_time = ['inc', 1];
                $allocate->save();

            } else if($diff > 0 && $allocate->notice_time == 2) {
                // 区域总监
                $map = [];
                $map[] = ['role_id', 'in', [5, 6]];
                $map[] = ['department_id', 'in', $pathArr];
                $ids = User::where($map)->column('dingding');
                if(!empty($ids)) {
                    // 第二次提醒，主管
                    $content = $user['realname'] . '有一条手机尾号为' . $mobileLast . "的客资未跟进\n";
                    $content .= '已经提醒3次';
                    $message = $DingModel->textMessage($content);
                    $DingModel->sendJobMessage($ids, $message);
                }

                if(!empty($user['dingding'])) {
                    $users[] = $user['dingding'];
                    $title = "[".date('Y-m-d')."]".'未跟进客资提醒';
                    $content = "有一条客资手机尾号为{$mobileLast}未跟进\n";
                    $content .= "提醒第{$times}次";
                    $url = "http://h5.hongsizg.com/pages/customer/mine?status=0&is_into_store=0&page_title=%E6%9C%AA%E8%B7%9F%E8%BF%9B%E5%AE%A2%E8%B5%84&time=".time();
                    $message = $DingModel->linkMessage($title, $content, $url);
                    $DingModel->sendJobMessage($users, $message);
                }
                $allocate->notice_time = ['inc', 1];
                $allocate->save();

            } else if($diff > 0 && $allocate->notice_time == 3) {
                // 区域总监
                $map = [];
                $map[] = ['role_id', 'in', [5, 6, 26]];
                $map[] = ['department_id', 'in', $pathArr];
                $ids = User::where($map)->column('dingding');
                if(!empty($ids)) {
                    // 第二次提醒，主管
                    $content = $user['realname'] . '有一条手机尾号为' . $mobileLast . "的客资未跟进\n";
                    $content .= "已经提醒{$times}次";
                    $message = $DingModel->textMessage($content);
                    $DingModel->sendJobMessage($ids, $message);
                }

                if(!empty($user['dingding'])) {
                    $users[] = $user['dingding'];
                    $title = "[".date('Y-m-d')."]".'未跟进客资提醒';
                    $content = "有一条客资手机尾号为{$mobileLast}未跟进\n";
                    $content .= "提醒第{$times}次";
                    $url = "http://h5.hongsizg.com/pages/customer/mine?status=0&is_into_store=0&page_title=%E6%9C%AA%E8%B7%9F%E8%BF%9B%E5%AE%A2%E8%B5%84&time=".time();
                    $message = $DingModel->linkMessage($title, $content, $url);
                    $DingModel->sendJobMessage($users, $message);
                }
                $allocate->notice_time = ['inc', 1];
                $allocate->save();
            } else if($diff > 0 && $allocate->notice_time == 4) {

                // 刘总账号
                $content = $user['realname'].'有一条手机尾号为'.$mobileLast."的客资未跟进\n";
                $content .= '已经提醒5次';
                $message = $DingModel->textMessage($content);
            }
        }
    }
}