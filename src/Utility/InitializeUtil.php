<?php

namespace EasySwoole\Skeleton\Utility;

use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Config;
use EasySwoole\HyperfOrm\MysqlPool;
use EasySwoole\Pool\Exception\Exception;
use EasySwoole\Pool\Manager;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\RedisPool\RedisPoolException;
use EasySwoole\Utility\File;
use EasySwoole\Skeleton\Helpers\Arrays\ArrayHelper;
use EasySwoole\Skeleton\Helpers\StringHelper;
use Swoole\Coroutine\Scheduler;
use Swoole\Timer;

/**
 * 初始化 工具
 *
 * Class InitializeUtil
 * @package App\Comment
 */
class InitializeUtil
{
    /**
     * 服务
     *
     * @param string $path
     * @param string $namespace
     */
    public static function serviceDi(string $path, string $namespace): void
    {
        if (StringHelper::isEmpty($path) || StringHelper::isEmpty($namespace)) {
            return;
        }
        $files = File::scanDirectory($path)['files'];
        if (StringHelper::isEmpty($files)) {
            return;
        }
        foreach ($files as $file) {
            $name = StringHelper::basename($file, '.php');
            $file = substr($file, 0, -4);
            $class = $namespace . implode('\\', array_map(function ($str) {
                    return StringHelper::ucfirst($str);
                }, StringHelper::explode(str_replace($path, '', $file), '/')));
            Di::getInstance()->alias($name, $class)->set($class, $class);
        }
    }

    /**
     * @param string $path
     */
    public static function config(string $path): void
    {
        if (StringHelper::isEmpty($path)) {
            return;
        }
        $data = [];
        $files = File::scanDirectory($path)['files'];
        if (StringHelper::isEmpty($files)) {
            return;
        }
        foreach ($files as $file) {
            $name = StringHelper::basename($file, '.php');
            $content = include $file;
            $data[$name] = $content;
        }
        Config::getInstance()->merge($data);
    }

    /**
     * @param array $paths
     */
    public static function di(array $paths): void
    {
        $paths[] = dirname(__DIR__) . '/Configs';
        foreach ($paths as $path) {
            $files = File::scanDirectory($path)['files'];
            if (StringHelper::isEmpty($files)) {
                continue;
            }
            foreach ($files as $file) {
                $name = StringHelper::basename($file, '.php');
                if ($name !== 'dependencies') {
                    continue;
                }
                $results = include $file;
                foreach ($results as $interface => $impl) {
                    if (is_int($interface)) {
                        Di::getInstance()->set($impl['key'], $impl['obj'], ...$impl['arg']);
                    } else {
                        Di::getInstance()->set($interface, $impl);
                    }
                }
            }
        }
    }

    /**
     * redis
     *
     * @param string $config
     *
     * @throws Exception
     * @throws \EasySwoole\RedisPool\Exception\Exception
     * @throws RedisPoolException
     */
    public static function redis(string $config = 'redis')
    {
        $redisPool = RedisPool::getInstance();
        if (!is_null($redisPool->getPool($config))) {
            return;
        }
        // redis
        $redisConfig = config($config);
        $redisPoolConfig = $redisPool->register(new RedisConfig($redisConfig), $config);
        // redis pool
        $redisPoolConfig->setIntervalCheckTime(ArrayHelper::getValue($redisConfig, 'intervalCheckTime', 30 * 1000));
        $redisPoolConfig->setMaxIdleTime(ArrayHelper::getValue($redisConfig, 'maxIdleTime', 15));
        $redisPoolConfig->setMinObjectNum(ArrayHelper::getValue($redisConfig, 'minObjectNum', 5));
        $redisPoolConfig->setMaxObjectNum(ArrayHelper::getValue($redisConfig, 'maxObjectNum', 60));
        $redisPoolConfig->setGetObjectTimeout(ArrayHelper::getValue($redisConfig, 'getObjectTimeout', 3));
    }

    /**
     * mysql
     *
     * @param string $config
     */
    public static function mysql(string $config = 'databases')
    {
        $databases = config($config);
        $manager = Manager::getInstance();
        foreach ($databases as $name => $conf) {
            if (!is_null($manager->get($name))) {
                continue;
            }
            Manager::getInstance()->register(new MysqlPool($conf), $name);
        }
    }

    /**
     *  主服务启动前调用协程 api
     *
     * @param string $redis
     * @param string $databaseKey
     */
    public static function scheduler(string $redis = 'redis', string $databaseKey = 'databases')
    {
        $scheduler = new Scheduler();
        $databases = config($databaseKey);
        $scheduler->add(function () use ($redis, $databases) {
            //注意,这3行代码只能放到最后面执行
            Timer::clearAll();
            // redis
            $redisPool = RedisPool::getInstance()->getPool($redis);
            if ($redisPool) {
                $redisPool->reset();
            }
            // db
            $manager = Manager::getInstance();
            foreach ($databases as $name => $database) {
                $db = $manager->get($name);
                if ($db) {
                    $db->reset();
                }
            }
        });
        $scheduler->start();
    }
}
