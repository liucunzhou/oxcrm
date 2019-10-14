<?php
namespace app\common\command;

use app\common\model\Member;
use app\common\model\BanquetHall;
use app\common\model\MemberAllocate;
use app\common\model\MemberVisit;
use app\common\model\Mobile;
use app\common\model\Source;
use app\common\model\Store;
use app\common\model\User;
use app\common\model\UserAuth;
use think\console\Command;
use think\console\command\make\Model;
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
            case 'dealMemberSourceText':
                $this->dealMemberSourceText();
                break;
            case 'separateOriginSourceText';
                $this->separateOriginSourceText($page);
                break;
            case 'syncMobileSet':
                $this->syncMobileSet($page);
                break;
            case 'syncCityId':
                $this->syncCityId($page);
                break;
            case 'syncSourceText':
                $this->syncSourceText($page);
                break;
        }
    }

    public function syncMobileSet($page)
    {
        $config = [
            'page' => $page
        ];

        $members = Member::withTrashed(true)->field('id,mobile,mobile1')->paginate(10000, false, $config);
        $mobileModel = new Mobile();
        foreach ($members as $member) {
            if(!empty($member->mobile))$mobileModel->insert(['mobile'=>$member->mobile,'member_id'=>$member->id]);
            if(!empty($member->mobile1))$mobileModel->insert(['mobile'=>$member->mobile1,'member_id'=>$member->id]);
        }
    }

    public function syncCityId($page)
    {
        $config = [
            'page' => $page
        ];
        $where = [];
        $where[] = ['city_id', '=', 0];
        $allocates = MemberAllocate::withTrashed(true)->field('id,member_id')->paginate(10000, false, $config);
        foreach ($allocates as $key=>$allocate) {
            $memberId = $allocate->member_id;
            $member = Member::get($memberId);
            $cityId = $member->city_id;
            $allocate->save(['city_id'=>$cityId]);
        }
    }

    public function syncSourceText($page)
    {
        $config = [
            'page' => $page
        ];
        $allocates = MemberAllocate::withTrashed(true)->paginate(10000, false, $config);
        foreach ($allocates as $key=>$allocate) {
            $memberId = $allocate->member_id;
            $member = Member::get($memberId);
            $sourceText = $member->source_text;
            $allocate->save(['source_text'=>$sourceText]);
        }
    }

    public function dealDuplicateAllocate()
    {
        $duplicates = MemberAllocate::field('member_id,user_id,count(*) as amount')->group('member_id,user_id')->having('amount > 1')->select();
        print_r($duplicates);
    }

    public function dealMemberSourceText()
    {
        $sources = Source::getSources();
        $map = [];
        $map[] = ['member_create_time', '>', '1567440000'];
        $map[] = ['source_id', '<>', 0];
        // $map[] = ['source_text', '=', ''];

        $members = MemberAllocate::where($map)->whereExp('source_text', 'is null')->select();
        // $members = MemberAllocate::where($map)->select();
        foreach ($members as $member) {

            $sourceId = $member->source_id;

            echo $sourceId."\n";
            $sourceText = $sources[$sourceId]['title'];
            echo $sourceText;
            echo "\n";
            $member->save(['source_text'=>$sourceText]);
            echo $member->getLastSql();
            echo "\n";
        }

    }

    public function separateOriginSourceText($page)
    {
        $config = [
            'page' => $page
        ];
        $where = "locate('/', source_text)";
        $members = MemberAllocate::withTrashed(true)->whereRaw($where)->paginate(5000, false, $config);
        foreach ($members as $member) {
            $sourceText = $member->source_text;
            $arr = explode('/', $sourceText);
            $first = array_shift($arr);
            $repeat = implode(',', $arr);
            $member->save(['source_text'=>$first]);
            echo $member->getLastSql();
            echo "\n";
        }
    }

    public function mergeMemberDump()
    {
        $MemberModel = new Member();
        $map = [];
        $list = $MemberModel->field('mobile,count(mobile) as amount')->where($map)->group('mobile')->having('amount > 1')->select();
        print_r($list);
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
                    $member_id = $member->id;
                    $member->delete(true);

                    $where1 = [];
                    $where1[] = ['member_id', '=', $member_id];
                    $MemberAllocate = new MemberAllocate();
                    $MemberAllocate->save(['member_id'=>$targetId], $where1);
                    echo $MemberAllocate->getLastSql();
                    echo "\n";
                    $where2 = [];
                    $where2[] = ['member_id', '=', $member_id];
                    $MemberVisit = new MemberVisit();
                    $MemberVisit->save(['member_id'=>$targetId], $where2);
                    echo $MemberAllocate->getLastSql();
                }
            }
        }
    }
}