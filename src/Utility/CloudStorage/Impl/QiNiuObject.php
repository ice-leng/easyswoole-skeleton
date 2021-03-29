<?php
declare(strict_types=1);

namespace EasySwoole\Skeleton\Utility\CloudStorage\Impl;

use EasySwoole\EasySwoole\Logger;
use EasySwoole\Skeleton\Errors\CommonError;
use EasySwoole\Skeleton\Framework\BizException;
use Qiniu\Auth;
use Qiniu\Processing\PersistentFop;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

class QiNiuObject
{
    
    private $accessKeyID;
    
    private $accessKeySecret;
    
    private $auth;
    
    public function __construct()
    {
        $this->accessKeyID     = config('thirdparty.qiniu.access_key');
        $this->accessKeySecret = config('thirdparty.qiniu.secret_key');
        $this->auth            = new Auth($this->accessKeyID, $this->accessKeySecret);
    }
    
    public function getUploadToken($bucket, $expires = 3600, $key = null, $policy = null)
    {
        try {
            return $this->auth->uploadToken($bucket, $key, $expires, $policy);
        } catch (\Throwable $e) {
            Logger::getInstance()->error($e->getMessage());
            throw new BizException(CommonError::SERVER_ERROR);
        }
    }
    
    public function uploadFile($token, $key, $filePath)
    {
        $uploadMgr = new UploadManager();
        
        try {
            return $uploadMgr->putFile($token, $key, $filePath);
        } catch (\Exception $e) {
            Logger::getInstance()->error($e->getMessage());
            throw new BizException(CommonError::SERVER_ERROR);
        }
    }
    
    public function fetch($url, $bucket, $key = ''): array
    {
        $bucketManager = new BucketManager($this->auth);
        
        return $bucketManager->fetch($url, $bucket, $key);
    }
    
    /**
     * 七牛资源打包
     * @param array $url        需打包资源url路径
     * @param $key              bucket 中存在的 资源
     * @param $bucket
     * @param $zipKey
     * @param int $mode         类型 2 小文件 4 大文件
     * @param bool $force       是否覆盖源文件
     * @param null $notify_url  异步通知的url
     * @param null $pipeline    异步队列名称 公用 null
     * @param null $config
     * @return array
     */
    public function mkZip(array $url, $key, $bucket, $zipKey, $mode = 2, $force = false, $notify_url = null, $pipeline = null, $config = null)
    {
        $pfop = new PersistentFop($this->auth, $config);
        
        $fops = "mkzip/{$mode}";
        foreach ($url as $value) {
            $fops .= '/url/' . \Qiniu\base64_urlSafeEncode($value);
        }
        $fops .= '|saveas/' . \Qiniu\base64_urlSafeEncode("$bucket:$zipKey");
        
        
        return $pfop->execute($bucket, $key, $fops, $pipeline, $notify_url, $force);
    }
    
    /**
     * 获取异步任务状态
     * @param $id
     * @param null $config
     * @return array
     */
    public function getPfopStatus($id, $config = null)
    {
        $pfop = new PersistentFop($this->auth, $config);
        
        return $pfop->status($id);
    }
}