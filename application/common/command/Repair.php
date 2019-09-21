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
                $this->dealMemberDump();
                break;
        }
    }

    public function dealMemberDump()
    {
        $MemberModel = new Member();
        $map = [];
        $map[] = ['active_status', '=', 5];
        $list = $MemberModel->field('mobile,count(mobile) as amount')->where($map)->group('mobile')->having('amount > 1')->select();
        foreach($list as $row) {
            $where = [];
            // $where[] = ['mobie', '=', $row];
            echo $row->mobile;
            echo "\n";
        }
    }
}