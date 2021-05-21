<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace EasySwoole\Skeleton;

use EasySwoole\Skeleton\Utility\Alipay\Alipay;
use EasySwoole\Skeleton\Utility\CloudStorage\Impl\AliyunOSS;
use EasySwoole\Skeleton\Utility\CloudStorage\Impl\QiNiuObject;
use EasySwoole\Skeleton\Utility\Sms\Impl\AliyunSms;
use EasySwoole\Skeleton\Utility\WeChat\WeChat;
use EasySwoole\Skeleton\Component\Cache\SimpleCache;
use Psr\SimpleCache\CacheInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                AliyunOSS::class   => AliyunOSS::class,
                QiNiuObject::class => QiNiuObject::class,
                AliyunSms::class   => AliyunSms::class,
                WeChat::class      => WeChat::class,
                Alipay::class      => Alipay::class,
                CacheInterface::class => SimpleCache::class,
            ],
            'publish' => [
                [
                    'id' => 'thirdparty',
                    'description' => 'The config for wx and aliyun.',
                    'source' => __DIR__ . '/Configs/thirdparty.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/Configs/thirdparty.php',
                ],
                [
                    'id' => 'redis',
                    'description' => 'The config for redis.',
                    'source' => __DIR__ . '/Configs/redis.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/Configs/redis.php',
                ],
                [
                    'id' => 'errorCode',
                    'description' => 'The config for errorCode.',
                    'source' => __DIR__ . '/Configs/errorCode.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/Configs/errorCode.php',
                ],
                [
                    'id' => 'cache',
                    'description' => 'The config for SimpleCache.',
                    'source' => __DIR__ . '/Configs/cache.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/Configs/cache.php',
                ],
                [
                    'id' => 'jwt',
                    'description' => 'The config for jwt.',
                    'source' => __DIR__ . '/Configs/jwt.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/Configs/jwt.php',
                ],
            ],
        ];
    }
}
