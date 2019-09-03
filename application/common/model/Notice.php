<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Notice extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    protected $noticeQueueKey = 'notice_queue';
    /**
     * @param array $source 来源 wash、recommend、dispatch、store、merchant,visit
     * @param bool $from
     * @param $to
     * @param $content
     * @return int|string
     */
    public function appendNotice($source, $from, $to, $noticeTime,$content)
    {
        $data['source'] = $source;
        $data['from_user_id'] = $from;
        $data['to_user_id'] = $to;
        $data['content'] = $content;
        $data['status'] = 0;
        $data['create_time'] = time();
        $data['notice_time'] = $noticeTime;

        $result = self::insert($data);
        $redis = redis();
        // $redis = new \Redis();
        $json = json_encode($data);
        $redis->rPush($this->noticeQueueKey, $json);
        return $result;
    }

    public function receiveNotice($noticeNo)
    {
        $data['receive_time'] = time();
        $data['status'] = 1;
        $where['notice_no'] = $noticeNo;
        $result = self::save($data, $where);

        return $result;
    }

    public function getNoticeFromQueue($userId)
    {
        $redis = redis();
        // $redis = new \Redis();
        $queue = $redis->lRange($this->noticeQueueKey, 0, -1);
        $time = time();
        foreach($queue as $key=>$value){
            if(empty($value)) continue;
            $notice = json_decode($value, 1);
            if($notice['to_user_id'] == $userId && $notice['source']=='visit' && $time > $notice['notice_time']) {
                return $notice;
            }

            if($notice['to_user_id'] == $userId && $notice['source']!='visit') {
                return $notice;
            }
        }

        return [];
    }
}