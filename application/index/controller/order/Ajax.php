<?php
namespace app\index\controller\order;

use app\wash\controller\Backend;

class Ajax extends Backend
{
    public function initialize()
    {
        parent::initialize();

        ## 获取所有品牌、公司
        $brands = \app\common\model\Brand::getBrands();
        $this->assign('brands', $brands);


        $rituals = \app\common\model\Ritual::getList();
        $this->assign('rituals', $rituals);

        $packages = \app\common\model\Package::getList();
        $this->assign('packages', $packages);

        $carList = \app\common\model\Car::getList();
        $this->assign('carList', $carList);

        $sugarList = \app\common\model\Sugar::getList();
        $this->assign('sugarList', $sugarList);

        $wineList = \app\common\model\Wine::getList();
        $this->assign('wineList', $wineList);

        $lightList = \app\common\model\Light::getList();
        $this->assign('lightList', $lightList);

        $dessertList = \app\common\model\Dessert::getList();
        $this->assign('dessertList', $dessertList);

        $ledList = \app\common\model\Led::getList();
        $this->assign('ledList', $ledList);

        $d3List = \app\common\model\D3::getList();
        $this->assign('d3List', $d3List);

        $staffs = \app\common\model\User::getUsers();
        $this->assign('staffs', $staffs);
    }

    // 婚庆表单
    public function wedding()
    {
        return $this->fetch();
    }

    // 婚宴表单
    public function banquet()
    {
        return $this->fetch();
    }

    // 酒店消费项目
    public function hotelItems()
    {
        return $this->fetch();
    }

    // 婚车信息
    public function car()
    {
        return $this->fetch();
    }

    // 喜糖信息
    public function sugar()
    {
        return $this->fetch();
    }

    // 酒水信息
    public function wine()
    {
        return $this->fetch();
    }

    // 灯光信息
    public function light()
    {
        return $this->fetch();
    }

    // 点心信息
    public function dessert()
    {
        return $this->fetch();
    }

    // led信息
    public function led()
    {
        return $this->fetch();
    }

    // 3D信息
    public function d3()
    {
        return $this->fetch();
    }
}