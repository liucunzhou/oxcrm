<?php
namespace app\index\controller;

use app\api\model\Qiniu;

class Images extends Base
{
    public function index()
    {
        // 获取分组
        $userId = $this->user['id'] ? $this->user['id'] : 0;
        $groups = model("ImageGroup")->where(['user_id'=>$userId])->select();
        $this->assign('groups', $groups);

        $get = $this->request->param();
        if(!empty($get['gid'])){
            // $option['map']['user_id'] = $this->user['id'];
            $option['map']['group_id'] = $get['gid'];
        } else {
            // $option['map']['user_id'] = $this->user['id'];
        }
        $list = model('Image')->order('create_time desc')->paginate(8);
        $this->assign('list', $list);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function delImage()
    {
        $id = input('id');
        $ImageModel = model("Image");
        $ImageModel->startTrans();
        $img = $ImageModel->find($id);
        $url = $img->getData("url");
        $fileName = basename($url);
        $delete = $img->delete();
        if($delete){
            $AK = 'GbUEldDtn2dmB5RhmT1n1QEO-rLoML0xs3dnB9o5';
            $SK = '-VWZeG9VLtJOW0L-V5MocavIHJN8ZDfKtNc18Np-';
            $Qiniu = new Qiniu($AK, $SK);
            $bucket = 'okmmt';
            $result = $Qiniu->delete($bucket, $fileName);
            if($result) {
                $ImageModel->commit();
                return json([
                    'code' => '200',
                    'msg' => '删除图片成功'
                ]);
            } else {
                $ImageModel->rollback();
                return json([
                   'code' => '600',
                    'msg' => '七牛云删除图片失败'
                ]);
            }
        } else {
            return json([
                'code'  => '500',
                'msg'   => '删除图片失败'
            ]);
        }
    }

    public function doUpload()
    {
        $file = $this->request->file('file');
        // 移动到框架应用根目录/uploads/ 目录下
        $dir = '../uploads';
        $info = $file->move($dir);
        if($info){
            // 初始化七牛云
            $AK = 'GbUEldDtn2dmB5RhmT1n1QEO-rLoML0xs3dnB9o5';
            $SK = '-VWZeG9VLtJOW0L-V5MocavIHJN8ZDfKtNc18Np-';
            $Qiniu = new Qiniu($AK, $SK);
            // 上传至对应的仓库
            $bucket = 'okmmt';
            $baseUrl = 'http://okmmt.u.qiniudn.com/';
            $fileName = $info->getFilename();
            $filePath = $dir.'/'.$info->getSaveName();
            $res = $Qiniu->upload($bucket, $fileName, $filePath);
            if($res) {
                // 添加到数据库
                $url = $baseUrl.$fileName;
                $data['url'] = $url;
                $data['status'] = 1;
                $addRes = model('Image')->save($data);
                if($addRes) {
                    unlink($filePath);
                    return json([
                        'code' => '200',
                        'result' => $url
                    ]);
                } else {
                    return json([
                        'code' => '500',
                        'result' => '录入数据库失败'
                    ]);
                }
            } else {
                return json([
                    'code' => '500',
                    'result' => '上传七牛云失败'
                ]);
            }
        }else{
            // 上传失败获取错误信息
            $error = $file->getError();
            return json([
                'code' => '500',
                'result' => $error
            ]);
        }
    }

    public function addGroup()
    {
        $groupName = input('groupName');
        if(empty($groupName)) {
            $this->ajaxReturn([
                'code'  => '400',
                'msg'   => '请输入分组名称'
            ]);
        }

        // 写入数据库
        $result = model('ImageGroup')->save([
            'user_id' => $this->user['id'] ? $this->user['id'] : 0,
            'group_name' => $groupName
        ]);

        if($result) {
            return json([
                'code' => '200',
                'msg' => '添加分组成功'
            ]);
        } else {
            return json([
                'code'  => '500',
                'msg'   => '添加分组成功'
            ]);
        }
    }
}