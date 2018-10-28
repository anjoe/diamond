<?php

namespace App\Http\Controllers\Upload;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use zgldh\QiniuStorage\QiniuStorage;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class UploadController extends Controller
{
    /**
     *上传凭证
     */
    public function getToken($buckets)
    {
        $accessKey = 'odNqqrGJhQS0ErPruL28TTyNNj6uj02fkhEtkNMh';
        $secretKey = 'oX139cFGH1KvcW2V0owWqVtnTtKSz0023JVRkd-W';
        $auth = new Auth($accessKey, $secretKey);
        $bucket = $buckets;//上传空间名称
        return $auth->uploadToken($bucket);//生成token
    }

    /**
     * 七牛文件上传
     */
    public function upload($file, $backets)
    {
        $token = $this->getToken($backets);
        $uploadManager = new UploadManager();
        $type = $file->getClientOriginalExtension();
        $filePath = $file->getRealPath();
        $name = md5(time().rand(1,999)) . '.' . $type;
        list($ret, $err) = $uploadManager->putFile($token, $name, $filePath,null, $type, false);
        if ($err) {//上传失败
            return false;
        } else {//成功
            return $ret['key'];
        }
    }

    /**
     *app调用接口 token
     */
    public function qiniu()
    {
        $result = $this->getToken('diamond');
        return $result;
    }
}
