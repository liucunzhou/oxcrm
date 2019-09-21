<?php
namespace app\common\command;

use app\common\model\Member;
use app\common\model\BanquetHall;
use app\common\model\MemberAllocate;
use app\common\model\MemberVisit;
use app\common\model\Store;
use app\common\model\User;
use app\common\model\UserAuth;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Repair extends Command
{
    protected function configure()
    {
        $this->setName("Repair")
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
            case 'dealMemberDump':
                $this->mergeMemberDump();
                break;
        }
    }

    public function mergeMemberDump()
    {
        $MemberModel = new Member();
        $map = [];
        $list = $MemberModel->field('mobile,count(mobile) as amount')->where($map)->group('mobile')->having('amount > 1')->select();
        foreach($list as $row) {
            $MemberObj = new Member();
            $mobile = $row->mobile;
            $where = [];
            $where[] = ['mobile', '=', $mobile];
            $members = $MemberObj->where($where)->order('create_time desc')->select();
            $ids = [];
            $targetId = 0;
            foreach($members as $key=>$member) {
                if($key == 0) {
                    $targetId = $member->id;
                } else {
                    $ids[] = $member->id;
                    $member->delete(true);

                    $where1 = [];
                    $where1[] = ['member_id', 'in', $ids];
                    $MemberAllocate = new MemberAllocate();
                    $MemberAllocate->save(['member_id'=>$targetId], $where1);
                    echo $MemberAllocate->getLastSql();
                    echo "\n";
                    $where2 = [];
                    $where2[] = ['member_id', 'in', $ids];
                    $MemberVisit = new MemberVisit();
                    $MemberVisit->save(['member_id'=>$targetId], $where2);
                    echo $MemberAllocate->getLastSql();
                }
            }
        }
    }
}