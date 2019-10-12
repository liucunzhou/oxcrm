<?php
namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

class Member extends Model
{
    protected $pk = 'id';
    // protected $autoWriteTimestamp = false;
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    /**
     * 获取电话号码对应的用户ID
     * @param $mobile
     * @return mixed
     */
    public static function checkMobile($mobile, $mobile1='')
    {

        if (empty($mobile1)) {
            $where = [];
            $where[] = ['mobile', '=', $mobile];
            $where1 = [];
            $where1[] = ['mobile1', '=', $mobile];
            $member = self::where($where)->whereOr($where1)->find();

        } else {
            ### 验证mobile
            $where = [];
            $where[] = ['mobile', '=', $mobile];
            $where1 = [];
            $where1[] = ['mobile1', '=', $mobile];
            ### 验证mobile1
            $where2 = [];
            $where2[] = ['mobile', '=', $mobile1];
            $where3 = [];
            $where3[] = ['mobile1', '=', $mobile1];
            $member = self::where($where)->whereOr($where1)->whereOr($where2)->whereOr($where3)->find();
        }

        return $member;
    }

    public static function checkPatchMobile($mobile) {

        $where = [];
        $where[] = ['mobile', '=', $mobile];
        $member = self::where($where)->find();

        return $member;
    }

    public static function checkFromMobileSet($mobile) {
        $where = [];
        $where[] = ['mobile', '=', $mobile];
        $member = Mobile::where($where)->find();
        return $member;
    }

    public static function updateRepeatLog($member, $sourceId, &$user, &$sources)
    {
        $isWriteDuplicate =false;
        $repeat = $sources[$sourceId]['title'];
        if(isset($sources[$sourceId]['parent_id'])) {
            $platformId = $sources[$sourceId]['parent_id'];
            $repeatPlatform = $sources[$platformId]['title'];
        } else {
            $repeatPlatform = '';
        }

        if(empty($member->repeat_log)) {
            $isWriteDuplicate = true;
        } else {
            ### 检测是否已存在title
            if(!empty($member->repeat_log) && !empty($repeat) && mb_strpos($member->repeat_log, $repeat)===false) {
                $repeat = $member->repeat_log.','.$repeat;
                $isWriteDuplicate = true;

                if(!empty($member->repeat_platform_log) && !empty($repeatPlatform) && mb_strpos($member->repeat_platform_log, $repeatPlatform)===false) {
                    $repeatPlatform = $member->repeat_platform_log.','.$repeatPlatform;
                }
            }
        }

        if($isWriteDuplicate) {
            $member->save(['repeat_log' => $repeat, 'repeat_platform_log'=>$repeatPlatform]);

            $map = [];
            $map[] = ['user_id', '=', $user['id']];
            $map[] = ['member_id', '=', $member->id];
            $map[] = ['source_id', '=', $sourceId];
            $repeatLogData = DuplicateLog::where($map)->find();
            if(empty($repeatLogData)) {
                ### 添加回访日志
                $data = [];
                $data['user_id'] = $user['id'];
                $data['member_id'] = $member->id;
                $data['member_no'] = $member->member_no;
                $data['source_id'] = $sourceId;
                $data['create_time'] = time();
                $DuplicateLogModel = new DuplicateLog();
                $DuplicateLogModel->insert($data);
            }
        }
    }

    /***
     * 更新客资缓存
     */
    public static function updateCache()
    {

    }
}