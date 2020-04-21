<?php

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Region extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    public static function getProvinceList()
    {
        $fields = 'id,pid,shortname,name,level,pinyin,code,zip_code';
        $list = self::where(['level' => 1])->order('sort desc')->column($fields, 'id');
        return $list;
    }

    public static function getCityList($provinceId = 0)
    {
        $map = [];
        if ($provinceId == 0) {
            $map[] = ['is_valid', '=', 1];
        } else {
            $map[] = ['pid', '=', $provinceId];
        }
        $map[] = ['level', '=', '2'];
        $fields = 'id,pid,shortname,name,level,pinyin,code,zip_code';
        $list = self::where($map)->order('sort desc')->column($fields, 'id');
        return $list;
    }

    public static function getCityListIndexOfShortname()
    {
        $cities = self::getCityList(0);
        $data = array_column($cities, 'id', 'shortname');

        return $data;
    }

    public static function getAreaList($cityId, $fields = '')
    {
        $map = [];
        $map[] = ['pid', '=', $cityId];
        $map[] = ['level', '=', '3'];
        if (empty($fields)) {
            $fields = 'id,pid,shortname,name,level,pinyin,code,zip_code';
        }
        $list = self::where($map)->order('sort desc')->column($fields, 'id');
        return $list;
    }
}