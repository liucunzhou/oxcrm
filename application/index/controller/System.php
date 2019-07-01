<?php
namespace app\index\controller;

use think\Db;
use think\facade\Request;

class System extends Base
{
    /**
     * 创建模型视图
     * @return mixed
     */
    public function createModel()
    {
        // 获取数据依赖路径
        $sql = "show tables";
        $tables = Db::query($sql);
        $this->assign('tables', $tables);

        // 获取保存目录路径
        $modelDirs = $this->getDirFileList('model');
        $this->assign('modelDirs', $modelDirs);
        return $this->fetch();
    }

    /**
     * 创建控制器视图
     * @return mixed
     */
    public function createController()
    {

        return $this->fetch();
    }

    /**
     * 创建表单视图
     * @return mixed
     */
    public function createForm()
    {
        return $this->fetch();
    }

    /**
     * 创建列表视图
     * @return mixed
     */
    public function createView()
    {
        $dbName = Db::getConfig('database');
        $field = $field = 'Tables_in_'.$dbName;
        $this->assign('field', $field);

        // 获取数据依赖路径
        $sql = "show tables";
        $tables = Db::query($sql);
        $this->assign('tables', $tables);

        // 获取保存目录路径
        $viewDirs = $this->getDirFileList('view');
        $this->assign('viewDirs', $viewDirs);
        return $this->fetch();
    }

    /**
     * 设置列表显示的字段名称
     * @return mixed
     */
    public function setTableViewFields()
    {
        $tableName = Request::post("table");
        $fields = Db::table($tableName)->getTableFields();
        $data = [];
        foreach ($fields as $field) {
            $type = Db::table($tableName)->getFieldsType($tableName, $field);
            $data[$field] = $type;
        }
        $this->assign('data', $data);

        // 获取依赖数据源路径
        $modelFiles = $this->getDirFileList('model');
        $this->assign('modelFiles', $modelFiles);
        return $this->fetch();
    }

    /**
     * 获取指定路径的源码，主要方便创建依赖时校验
     * @return mixed
     */
    public function getSourceCode()
    {
        $file = Request::post("source");
        $code = '';
        if (is_file($file)) {
            $code = file_get_contents($file);
        }
        $this->assign('code', $code);

        return $this->fetch();
    }

    /**
     * 获取应用下的某个目录下的文件或者文件夹
     * @param string $type model，controller, view, validate
     * @return array
     */
    private function getDirFileList($type='model')
    {
        $path = $this->app->getModulePath().$type;
        $fileNames = [];
        if(is_dir($path)) {
            $dirs = dir($path);
            while (false !== ($entry = $dirs->read())) {
                if($entry == '.' || $entry == '..') continue;
                $fileNames[$entry] = $path.'/'.$entry;
            }
        }

        return $fileNames;
    }
}
