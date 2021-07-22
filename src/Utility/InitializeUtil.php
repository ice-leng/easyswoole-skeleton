<?php

namespace EasySwoole\Skeleton\Utility;

use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\SysConst;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HyperfOrm\MysqlPool;
use EasySwoole\Pool\Exception\Exception;
use EasySwoole\Pool\Manager;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\RedisPool\RedisPoolException;
use EasySwoole\Socket\Dispatcher;
use EasySwoole\Utility\File;
use EasySwoole\Skeleton\Helpers\Arrays\ArrayHelper;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\Skeleton\Helpers\StringHelper;
use EasySwoole\EasySwoole\Crontab\Crontab;
use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;
use Swoole\Coroutine\Scheduler;
use Swoole\Timer;
use Throwable;

/**
 * 初始化 工具
 *
 * Class InitializeUtil
 * @package App\Comment
 */
class InitializeUtil
{

    /**
     * 跨域
     *
     * @param callable|null $call
     */
    public static function cors(?callable $call = null)
    {
        // 实现 onRequest 事件
        Di::getInstance()->set(SysConst::HTTP_GLOBAL_ON_REQUEST, function (Request $request, Response $response) use ($call): bool {
            ###### 处理请求的跨域问题 ######
            $response->withHeader('Access-Control-Allow-Origin', '*');
            $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
            $response->withHeader('Access-Control-Allow-Credentials', 'true');
            $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Token, responseType');
            if ($request->getMethod() === 'OPTIONS') {
                $response->withStatus(Status::CODE_OK);
                return false;
            }
            if (StringHelper::matchWildcard("*favicon*", $request->getUri()->getPath())) {
                $response->withStatus(Status::CODE_OK);
                return false;
            }
            if (!is_null($call)) {
                return call_user_func($call, $request, $response);
            }
            return true;
        });
    }

    /**
     * 服务
     *
     * @param string|null $path
     * @param string|null $namespace
     */
    public static function serviceDi(?string $path = null, string $namespace = 'App\\Service'): void
    {
        $path = $path ?? EASYSWOOLE_ROOT . '/App/Service';

        $scan = File::scanDirectory($path);
        if (!$scan) {
            return;
        }
        $files = $scan['files'];
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
     * @param string|null $path
     */
    public static function config(?string $path = null): void
    {
        $path = $path ?? EASYSWOOLE_ROOT . '/App/Configs';
        $data = [];
        $scan = File::scanDirectory($path);
        if (!$scan) {
            return;
        }
        $files = $scan['files'];
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
     * @param string|null $path
     *
     * @throws Throwable
     */
    public static function di(?string $path = null): void
    {
        $dependencies = [];
        $file = $path ?? EASYSWOOLE_ROOT . '/App/Configs/dependencies.php';
        if (is_file($file)) {
            $dependencies = include $file;
        }
        foreach ($dependencies as $interface => $impl) {
            if (is_array($impl) && isset($impl['key'], $impl['obj'], $impl['arg'])) {
                Di::getInstance()->set($impl['key'], $impl['obj'], ...$impl['arg']);
            } else {
                Di::getInstance()->set($interface, $impl);
            }
        }
        /**
         * @var ContainerInterface $container
         */
        $container = Di::getInstance()->get(ContainerInterface::class);
        if ($container) {
            ApplicationContext::setContainer($container);
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

    /**
     * 进程
     *
     * @param array $classes
     * @param bool  $isOpen
     */
    public static function process(array $classes, bool $isOpen = true)
    {
        if (!$isOpen) {
            return;
        }
        foreach ($classes as $class) {
            $processConfig = new \EasySwoole\Component\Process\Config();
            $processConfig->setProcessName($class);//设置进程名称
            $processConfig->setProcessGroup($class);//设置进程组
            $processConfig->setRedirectStdinStdout(false);//是否重定向标准io
            $processConfig->setPipeType($processConfig::PIPE_TYPE_SOCK_DGRAM);//设置管道类型
            $processConfig->setEnableCoroutine(true);//是否自动开启协程
            $processConfig->setMaxExitWaitTime(3);//最大退出等待时间
            \EasySwoole\Component\Process\Manager::getInstance()->addProcess(new $class($processConfig));
        }
    }

    /**
     * websocket
     *
     * @param EventRegister $register
     * @param               $parser
     *
     * @throws \EasySwoole\Socket\Exception\Exception
     */
    public static function webSocket(EventRegister $register, $parser)
    {
        // 创建一个 Dispatcher 配置
        $conf = new \EasySwoole\Socket\Config();
        // 设置 Dispatcher 为 WebSocket 模式
        $conf->setType(\EasySwoole\Socket\Config::WEB_SOCKET);
        // 设置解析器对象
        $conf->setParser($parser);
        // 创建 Dispatcher 对象 并注入 config 对象
        $dispatch = new Dispatcher($conf);
        // 给server 注册相关事件 在 WebSocket 模式下  on message 事件必须注册 并且交给 Dispatcher 对象处理
        $register->set(EventRegister::onMessage, function (\swoole_websocket_server $server, \swoole_websocket_frame $frame) use ($dispatch) {
            $dispatch->dispatch($server, $frame->data, $frame);
        });
    }

    /**
     * task
     * @param array $classes
     */
    public static function task(array $classes)
    {
        foreach ($classes as $class) {
            Crontab::getInstance()->addTask($class);
        }
    }
}
