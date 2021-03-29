<?php

use EasySwoole\Skeleton\Utility\Alipay\Alipay;
use EasySwoole\Skeleton\Utility\CloudStorage\Impl\AliyunOSS;
use EasySwoole\Skeleton\Utility\CloudStorage\Impl\QiNiuObject;
use EasySwoole\Skeleton\Utility\Sms\Impl\AliyunSms;
use EasySwoole\WeChat\WeChat;

return [
    AliyunOSS::class   => AliyunOSS::class,
    QiNiuObject::class => QiNiuObject::class,
    AliyunSms::class   => AliyunSms::class,
    WeChat::class      => WeChat::class,
    Alipay::class      => Alipay::class,
];
