<?php
declare(strict_types=1);


namespace EasySwoole\Skeleton\Utility\CloudStorage;


use EasySwoole\Component\Di;
use EasySwoole\Skeleton\Utility\CloudStorage\Impl\AliyunOSS;
use EasySwoole\Skeleton\Utility\CloudStorage\Impl\QiNiuObject;

class CloudStorage
{
    const ALIYUN_OSS = AliyunOSS::class;
    
    const QI_NIU_OBJECT = QiNiuObject::class;

    /**
     * @param $name
     * @return AbstractCloudStorage
     */
    public static function instance($name): AbstractCloudStorage
    {
        return Di::getInstance()->get($name);
    }
}
