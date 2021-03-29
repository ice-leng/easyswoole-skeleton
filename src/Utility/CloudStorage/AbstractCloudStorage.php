<?php
declare(strict_types=1);

namespace EasySwoole\Skeleton\Utility\CloudStorage;

interface AbstractCloudStorage
{
    public function getUploadToken($bucket, $key = null);

    public function getSignUrl($bucket, string $key, string $dir = '/');

    public function uploadObject(string $bucket, string $key, string $content);
    
    public function uploadFile($bucket, $object, $file, $options = NULL);
    
    public function getObject($bucket, $object, $options = NULL);
}