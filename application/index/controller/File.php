<?php
namespace app\index\controller;

class File extends Base
{
    public function upload()
    {
        $file = request()->file("file");
        $info = $file->move("../uploads");
        if ($info) {
            echo $info->name;
            print_r($info);
            $fileName = $info->getPathname();

            return json(['code' => '200', 'msg' => '上传成功,请继续分配']);
        } else {
            return json(['code' => '500', 'msg' => '上传失败']);
        }
    }
}