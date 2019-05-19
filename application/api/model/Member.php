<?php
namespace app\api\model;

use think\facade\Cache;
use think\Model;

class Member extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = 'datetime';
    protected static $Redis = null;

    public function initialize()
    {
        parent::initialize();
        self::$Redis = Cache::init();
    }

    /**
     * @param $openid 小程序提供的openid
     * @return array|null|\PDOStatement|string|Model|void
     */
    public function checkUserExist($openid)
    {
        // $redis = new \Redis();
        $chacheKey = $openid;
        $member = self::$Redis->handler()->hGetAll($chacheKey);
        // 检测缓存
        if(empty($member)) {
            $member = model('Member')->where(['openid'=>$openid])->find();

            if(!empty($member)) {
                // 设置用户缓存
                $cache = self::$Redis->handler()->hMset($chacheKey, $this->toArray($member));
            } else {
                $member = $this->doAddUserByOpenid($openid);
                $member && self::$Redis->handler()->hMset($chacheKey, $this->toArray($member));
            }
        }

        return $member;
    }

    /**
     * 添加用户
     * @param $openid 小程序提供的openid
     * @return array|null|\PDOStatement|string|Model
     */
    public function doAddUserByOpenid($openid)
    {
        $model = model('Member');
        $data['openid'] = $openid;
        $res = $model->save($data);
        if($res) {
            $member = $model->where(['openid'=>$openid])->find()->toArray();
        } else {
            $member = [];
        }

        return $member;
    }

    public function updateCache($openid) {

    }
}