<?php

use EasySwoole\Skeleton\Utility\Alipay\Alipay;
use EasySwoole\Skeleton\Utility\CloudStorage\Impl\AliyunOSS;
use EasySwoole\Skeleton\Utility\CloudStorage\Impl\QiNiuObject;
use EasySwoole\Skeleton\Utility\Sms\Impl\AliyunSms;
use EasySwoole\Skeleton\Utility\WeChat\WeChat;
use EasySwoole\Skeleton\Component\Cache\SimpleCache;
use Psr\SimpleCache\CacheInterface;

return [
    AliyunOSS::class   => AliyunOSS::class,
    QiNiuObject::class => QiNiuObject::class,
    AliyunSms::class   => AliyunSms::class,
    WeChat::class      => WeChat::class,
    Alipay::class      => Alipay::class,
    CacheInterface::class => SimpleCache::class,
];
