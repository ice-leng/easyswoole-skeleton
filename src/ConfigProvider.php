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
                    'id' => 'app',
                    'description' => 'The config app.',
                    'source' => __DIR__ . '/Configs/app.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/Configs/app.php',
                ],
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
                [
                    'id' => 'index',
                    'description' => 'The config for controller index.',
                    'source' => dirname(__DIR__) . '/publish/Index.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/HttpController/Index.php',
                ],
                [
                    'id' => 'router',
                    'description' => 'The config for controller router.',
                    'source' => dirname(__DIR__) . '/publish/Router.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/HttpController/Router.php',
                ],
                [
                    'id' => 'swagger',
                    'description' => 'The config for controller swagger.',
                    'source' => dirname(__DIR__) . '/publish/Swagger.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/HttpController/Swagger.php',
                ],
                [
                    'id' => 'dev',
                    'description' => 'The config dev',
                    'source' => dirname(__DIR__) . '/publish/dev.php',
                    'destination' => EASYSWOOLE_ROOT . '/dev.php',
                ],
                [
                    'id' => 'produce',
                    'description' => 'The config produce',
                    'source' => dirname(__DIR__) . '/publish/produce.php',
                    'destination' => EASYSWOOLE_ROOT . '/produce.php',
                ],
                [
                    'id' => 'easyswooleEvent',
                    'description' => 'The config easyswoole event',
                    'source' => dirname(__DIR__) . '/publish/EasySwooleEvent.php',
                    'destination' => EASYSWOOLE_ROOT . '/EasySwooleEvent.php',
                ]
            ],
        ];
    }
}
