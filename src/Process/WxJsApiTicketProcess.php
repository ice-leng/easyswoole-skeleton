<?php

namespace EasySwoole\Skeleton\Process;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\Skeleton\Utility\WeChat\WeChat;
use EasySwoole\Spl\SplArray;
use Swoole\Coroutine;
use Throwable;

class WxJsApiTicketProcess extends AbstractProcess
{
    protected function run($arg)
    {
        Coroutine::create(function () {
            while (1) {
                try {
                    $weChat = new WeChat();
                    $config = new SplArray(config('thirdparty.wechat'));
                    if ($config->get('appId') && $config->get('appSecret')) {
                        $weChat->officialAccount()->jsApi()->sdk()->jsTicket()->refreshTicket();
                    }
                } catch (Throwable $exception) {
                    Logger::getInstance()->error(format_throwable($exception));
                }
                Coroutine::sleep(7110);
            }
        });
    }
}
