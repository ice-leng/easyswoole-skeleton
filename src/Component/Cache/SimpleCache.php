<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/4/17
 * Time:  12:04 上午
 */

namespace EasySwoole\Skeleton\Component\Cache;

use Psr\SimpleCache\CacheInterface;

class SimpleCache implements CacheInterface
{

    protected $driver;

    public function __construct(SimpleCacheConfig $config)
    {
        $driver = $config->getDriver();
        $this->driver = new $driver([
            'dir'    => $config->getDir(),
            'prefix' => $config->getPrefix(),
            'packer' => $config->getPacker(),
        ]);
    }

    public function __call($name, $arguments)
    {
        return $this->driver->{$name}(...$arguments);
    }

    public function get($key, $default = null)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function set($key, $value, $ttl = null)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function delete($key)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function clear()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getMultiple($keys, $default = null)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function setMultiple($values, $ttl = null)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function deleteMultiple($keys)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function has($key)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }
}