<?php
namespace app\h5\controller\order;

use app\h5\controller\Base;

class Upload extends Base
{
    protected function initialize()
    {
        return parent::initialize();
    }

    public function doupload()
    {

        $baseUrl = $this->request->server('REQUEST_SCHEME').'://'.$this->request->host();
        $file = request()->file("file");
        $info = $file->move("uploads/order");
        if ($info) {

            $result = [
                'code'  => '200',
                'msg'   => '上传成功',
                'image' => $baseUrl.'/'.$info->getPathname()
            ];
        } else {
            $result = [
                'code'  => '500',
                'msg'   => '上传失败'
            ];
        }

        return json($result);
    }
}