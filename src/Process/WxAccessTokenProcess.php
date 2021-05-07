<?php

namespace EasySwoole\Skeleton\Process;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\Skeleton\Utility\WeChat\WeChat;
use EasySwoole\Spl\SplArray;
use Swoole\Coroutine;
use Throwable;

class WxAccessTokenProcess extends AbstractProcess
{
    protected function run($arg)
    {
        Coroutine::create(function () {
            while (1) {
                try {
                    $weChat = new WeChat();
                    $config = new SplArray(config('thirdparty.wechat'));
                    $miniProgramAccessToken = $weChat->miniProgram()->accessToken();
                    if ($config->get('app_id') && $config->get('app_secret') && !$miniProgramAccessToken->getToken()) {
                        $miniProgramAccessToken->refresh();
                    }

                    $officialAccountAccessToken = $weChat->officialAccount()->accessToken();
                    if ($config->get('appId') && $config->get('appSecret') && !$officialAccountAccessToken->getToken()) {
                        $officialAccountAccessToken->refresh();
                    }
                } catch (Throwable $exception) {
                    Logger::getInstance()->error(format_throwable($exception));
                }
                Coroutine::sleep(7180);
            }
        });
    }
}
