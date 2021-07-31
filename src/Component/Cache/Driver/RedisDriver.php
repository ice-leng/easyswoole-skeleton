<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/4/16
 * Time:  11:54 下午
 */

namespace EasySwoole\Skeleton\Component\Cache\Driver;

use EasySwoole\RedisPool\RedisPool;
use Psr\SimpleCache\CacheInterface;

class RedisDriver extends Driver implements CacheInterface
{

    protected function getRedis()
    {
        return RedisPool::defer('redis');
    }

    public function get($key, $default = null)
    {
        $res = $this->getRedis()->get($this->getCacheKey($key));
        if ($res === false || is_null($res)) {
            return $default;
        }

        return $this->packer->unpack($res);
    }

    public function set($key, $value, $ttl = null)
    {
        $res = $this->packer->pack($value);
        return $this->getRedis()->set($this->getCacheKey($key), $res, $ttl ?? 0);
    }

    public function delete($key)
    {
        return (bool)$this->getRedis()->del($this->getCacheKey($key));
    }

    public function clear()
    {
        $iterator = null;
        $key = '*';
        $redis = $this->getRedis();
        while (true) {
            $keys = $redis->scan($iterator, $this->getCacheKey($key), 10000);
            if (!empty($keys)) {
                $redis->del(...$keys);
            }
            if (empty($iterator)) {
                break;
            }
        }
        return true;
    }

    public function getMultiple($keys, $default = null)
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $cacheKeys = array_map(function ($key) {
            return $this->getCacheKey($key);
        }, $keys);

        $values = $this->getRedis()->mget($cacheKeys);
        $result = [];
        foreach ($keys as $i => $key) {
            $result[$key] = $values[$i] === false ? $default : $this->packer->unpack($values[$i]);
        }

        return $result;
    }

    public function setMultiple($values, $ttl = null)
    {
        if (!is_array($values)) {
            $values = [$values];
        }
        $cacheKeys = [];
        foreach ($values as $key => $value) {
            $cacheKeys[$this->getCacheKey($key)] = $this->packer->pack($value);
        }

        $redis = $this->getRedis();
        if ($ttl > 0) {
            foreach ($cacheKeys as $key => $value) {
                $redis->set($key, $value, $ttl);
            }
            return true;
        }
        return $redis->mset($cacheKeys);
    }

    public function deleteMultiple($keys)
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $cacheKeys = array_map(function ($key) {
            return $this->getCacheKey($key);
        }, $keys);

        return (bool)$this->getRedis()->del(...$cacheKeys);
    }

    public function has($key)
    {
        return (bool)$this->getRedis()->exists($this->getCacheKey($key));
    }
}
