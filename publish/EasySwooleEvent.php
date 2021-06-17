<?php

namespace EasySwoole\EasySwoole;

use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\Skeleton\Utility\InitializeUtil;
use Swlib\SaberGM;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
        bcscale(2);
        SaberGM::default([
            'exception_report' => 0,
            'use_pool'         => true,
        ]);

        // config
        InitializeUtil::config();

        // di
        InitializeUtil::di();

        // service
        InitializeUtil::serviceDi();

        // redis
        InitializeUtil::redis();

        // mysql
        InitializeUtil::mysql();

        // scheduler
        InitializeUtil::scheduler();
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // 进程任务

    }
}
