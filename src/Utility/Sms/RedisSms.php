<?php

namespace EasySwoole\Skeleton\Utility\WeChat;

use EasySwoole\RedisPool\RedisPool;

class RedisSms
{

    const SMS_CODE_PREFIX = 'sms_code_%s_%s';

    public function key(string $mobile, string $template): string
    {
        return sprintf(self::SMS_CODE_PREFIX, $template, $mobile);
    }

    public function get(string $mobile, string $template)
    {
        return RedisPool::defer('redis')->get($this->key($mobile, $template));
    }

    public function set(string $mobile, string $template, $value, int $expire = null)
    {
        RedisPool::defer('redis')->set($this->key($mobile, $template), $value, $expire);
    }
}
