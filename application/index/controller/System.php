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
     * 生成表单页面
     */
    public function doCreateForm()
    {
        $post = Request::post();

        print_r($post);
        $fields = $post['field'];
        $this->assign('fields', $fields);

        $html = $this->fetch('form_model');
        $path = $post['dir'].'/'.$post['file_name'].'.html';
        $result = file_put_contents($path, $html);

        if($result > 0) {
            return json(['code' => '200', 'msg' => '生成表单成功']);
        } else {
            return json(['code' => '500', 'msg' => '生成表单失败']);
        }

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
        $controllers = $this->getDirFileList('controller');
        $this->assign('controllers', $controllers);

        // 获取保存目录路径
        $viewDirs = $this->getDirFileList('view');
        $this->assign('viewDirs', $viewDirs);
        return $this->fetch();
    }

    public function doCreateView()
    {
        $post = Request::post();
        print_r($post);

        // 设置显示字段
        $cols = [];
        $fields = $post['field'];
        foreach ($fields as $key=>$field) {
            $row = [];
            // 检测是否显示 checkbox
            if($key=='checkbox' && $field['display'] == 1) {
                $row['type'] = $key;
                $row['width'] = !empty($field['width']) ? $field['width'] : '120';
                $field['fixed']!='none' && $row['fixed'] = $field['fixed'];
            } else if($key == 'tool' && $field['display'] == 1) {
                $row['type'] = 'tool';
                $row['title'] = '操作';
                $row['toolbar'] = '#table-tool';
                $row['width'] = !empty($field['width']) ? $field['width'] : '120';
                $field['fixed']!='none' && $row['fixed'] = $field['fixed'];
            } else if($field['display'] == 1) {
                $row['field'] = $key;
                $row['title'] = $field['title']?$field['title']:$key;
                if($field['width'] != 'full') {
                    $row['width'] = !empty($field['width']) ? $field['width'] : '120';
                }

                $field['fixed']!='none' && $row['fixed'] = $field['fixed'];
                $field['edit'] && $field['edit'] = true;
                $field['sort'] && $field['sort'] = true;
            } else {
                continue;
            }
            !empty($row) && $cols[] = $row;
        }
        $colsJson = json_encode($cols, JSON_NUMERIC_CHECK);
        $this->assign('cols', $colsJson);

        // 设置toolbar
        $toolbars = $post['toolbar'];
        print_r($toolbars);
        $this->assign('toolbars', $toolbars);

        $tools = $post['tool'];
        $this->assign('tools', $tools);

        // 保存生成的页面
        $html = $this->fetch('table_model');
        $path = $post['table-name'].'/'.$post['file_name'].'.html';
        $result = file_put_contents($path, $html);

        if($result > 0) {
            return json(['code' => '200', 'msg' => '生成表单成功']);
        } else {
            return json(['code' => '500', 'msg' => '生成表单失败']);
        }
    }

    /***
     * 获取模块下的所有列表
     */
    public function getControllerActions()
    {
        $fileName = Request::post("fileName");
        $pathinfo = pathinfo($fileName);
        $contorllerName = $pathinfo['filename'];
        $appPath = $this->app->getAppPath();
        $fileName = str_replace($appPath, '', $fileName);
        $fileName = str_replace('/', '\\', $fileName);
        $className = substr($fileName,0, -4);
        $className = "\app\\".$className;
        $class = new $className;
        $functions = get_class_methods($class);
        $data = [];
        foreach ($functions as $val){
            $data[$val] = $contorllerName.'/'.$val;
        }

        return json($data);
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
     * 设置列表显示的字段名称
     * @return mixed
     */
    public function setFormViewFields()
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

    private function gitForcePush($message)
    {
        echo $message;
        echo "<br>";
        // exec("cd /data/platform; git add .; git commit -m '{$message}'");
        $result = exec("cd /data/platform");
        var_dump($result);
        echo "<br>";
        echo exec("git add .");
        echo "<br>";
        echo exec("git commit -m '{$message}'");
        echo "<br>";
        //exec("git reset --hard");
        echo exec("git push -f");
        echo "<br>";
    }
}
