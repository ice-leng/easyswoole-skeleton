<?php

namespace EasySwoole\Skeleton\Utility;

use EasySwoole\RedisPool\RedisPool;

class RedisCache extends AbstractRedisCache
{

    public function getRedis()
    {
        return RedisPool::defer('redis');
    }

    public function getConfig(): array
    {
        return config('redis');
    }
}
