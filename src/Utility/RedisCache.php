<?php

namespace EasySwooleTools\Skeleton\Utility;

use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Skeleton\Utility\AbstractRedisCache;

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
