<?php

use EasySwoole\Skeleton\Utility\CloudStorage\CloudStorage;
use EasySwoole\Skeleton\Utility\CloudStorage\Impl\AliyunOSS;
use EasySwoole\Skeleton\Utility\CloudStorage\Impl\QiNiuObject;
use EasySwoole\Skeleton\Utility\Sms\Impl\AliyunSms;

return [
    AliyunOSS::class   => AliyunOSS::class,
    QiNiuObject::class => QiNiuObject::class,
    AliyunSms::class   => AliyunSms::class,
];
