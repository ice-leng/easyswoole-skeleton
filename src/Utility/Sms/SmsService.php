<?php
declare(strict_types=1);

namespace EasySwoole\Skeleton\Utility\Sms;

use EasySwoole\Component\Di;
use EasySwoole\Skeleton\Utility\Sms\Impl\AliyunSms;

class SmsService
{
    const ALIYUN = AliyunSms::class;

    /**
     * @param $name
     * @return Sms
     */
    public static function instance($name)
    {
        return Di::getInstance()->get($name);
    }
}