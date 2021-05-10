<?php

namespace EasySwoole\Skeleton\Utility\WeChat;

use EasySwoole\RedisPool\RedisPool;
use EasySwoole\WeChat\AbstractInterface\StorageInterface;

class RedisStorage implements StorageInterface
{

    /**
     * @var string
     */
    private $prefix;

    public function __construct(string $tempDir, $appId)
    {
        $this->prefix = $appId . '.';
    }

    public function get($key)
    {
        return RedisPool::defer('redis')->get($this->prefix . $key);
    }

    public function set($key, $value, int $expire = null)
    {
        if ($expire > time()) {
            $expire -= time();
        }
        RedisPool::defer('redis')->set($this->prefix . $key, $value, $expire);
    }
}
