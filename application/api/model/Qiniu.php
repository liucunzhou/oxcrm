<?php
namespace app\api\model;
use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

class Qiniu
{
    private $AK = '';
    private $SK = '';
    private $Auth = null;

    public function __construct($AK, $SK)
    {
        $this->AK = $AK;
        $this->SK = $SK;
        $this->Auth = new Auth($this->AK, $this->SK);
    }

    public function upload($bucket, $fileName, $filePath)
    {
        $token = $this->Auth->uploadToken($bucket);
        $uploadMgr = new UploadManager();
        list($ret, $err) = $uploadMgr->putFile($token, $fileName, $filePath);
        if ($err !== null) {
            return false;
        } else {
            return $ret;
        }
    }

    public function delete($bucket, $fileName) {
        $config = new Config();
        $Bucket = new BucketManager($this->Auth, $config);
        // 成功返回null
        $result = $Bucket->delete($bucket, $fileName);
        if ($result) {
            return false;
        } else {
            return true;
        }
    }
}