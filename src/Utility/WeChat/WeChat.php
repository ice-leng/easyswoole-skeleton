<?php
declare(strict_types=1);

namespace EasySwoole\Skeleton\Utility\WeChat;

use EasySwoole\Spl\SplArray;
use EasySwoole\WeChat\Config;

class WeChat extends \EasySwoole\WeChat\WeChat
{
    public function __construct()
    {
        $wechat = new SplArray(config('thirdparty.wechat'));
        $config = new Config();

        if ($wechat->get('app_id') && $wechat->get('app_secret')) {
            $config->miniProgram()->setAppId($wechat->get('app_id'));
            $config->miniProgram()->setAppSecret($wechat->get('app_secret'));
            $config->miniProgram()->setStorage(new RedisStorage('', $wechat->get('app_id')));
        }

        if ($wechat->get('appId') && $wechat->get('appSecret')) {
            $config->officialAccount()->setAppId($wechat->get('appId'));
            $config->officialAccount()->setAppSecret($wechat->get('appSecret'));
            $config->officialAccount()->setStorage(new RedisStorage('', $wechat->get('appId')));
        }

        parent::__construct($config);
    }



}
