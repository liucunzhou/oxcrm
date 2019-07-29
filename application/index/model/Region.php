<?php
namespace app\index\model;

use think\Model;
use think\model\concern\SoftDelete;

class Region extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function getProvinceList()
    {
        $fields = 'id,pid,shortname,name,level,pinyin,code,zip_code';
        $list = self::where(['level'=>1])->order('sort desc')->column($fields, 'id');
        return $list;
    }

    public static function getCityList($provinceId)
    {
        $map = [];
        $map[] = ['pid', '=', $provinceId];
        $map[] = ['level', '=', '2'];
        $fields = 'id,pid,shortname,name,level,pinyin,code,zip_code';
        $list = self::where($map)->order('sort desc')->column($fields, 'id');
        return $list;
    }

    public static function getAreaList($cityId) {
        $map = [];
        $map[] = ['pid', '=', $cityId];
        $map[] = ['level', '=', '3'];
        $fields = 'id,pid,shortname,name,level,pinyin,code,zip_code';
        $list = self::where($map)->order('sort desc')->column($fields, 'id');
        return $list;
    }
}