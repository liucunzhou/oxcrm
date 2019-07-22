<?php
namespace app\index\model;


use think\Model;
use think\model\concern\SoftDelete;

class Store extends Model
{
    protected $pk = 'id';
    // protected $autoWriteTimestamp = 'datetime';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function getStore($id,$update=false)
    {
        $cacheKey = 'store';
        $data = redis()->hGet($cacheKey, $id);
        $data = json_decode($data, true);
        if(empty($data) || $update) {
            $map[] = ['id', '=', $id];
            $data = self::where($map)->find()->toArray();
            $json = json_encode($data);
            $result = redis()->hSet($cacheKey, $id, $json);
        }

        return $data;
    }

    public static function getStoreList($update=false)
    {
        $cacheKey = 'stores';
        $data = redis()->get($cacheKey);
        if(empty($data) || $update) {
            $data = self::order('is_valid desc,sort desc,id asc')->column('id,brand_id,title', 'id');
            $json = json_encode($data);
            $result = redis()->set($cacheKey, $json);
        } else {

            $data = json_decode($data, true);
        }

        return $data;
    }

    public static function getStoresGroupByBrand()
    {
        $brands = Brand::getBrands();
        $stores = self::getStoreList();

        foreach ($brands as $key=>&$brand) {
            foreach ($stores as $store) {
                if($store['brand_id'] == $key) $brand['stores'][] = $store;
            }
        }

        return $brands;
    }

    public static function getStoresWithUser()
    {
        $stores = Store::getStoreList();
        $users = User::getUsers();
        foreach ($stores as &$store) {
            foreach ($users as $user) {
                $auth = UserAuth::getUserLogicAuth($user['id']);
                if(!empty($auth['store_ids'])) {
                    $ids = explode(',', $auth['store_ids']);
                    if (in_array($store['id'], $ids)) {
                        $store['users'][] = $user;
                    }
                } else {
                    continue;
                }
            }
        }

        return $stores;
    }

    public static function updateCache()
    {
        self::getStoreList(true);
    }
}