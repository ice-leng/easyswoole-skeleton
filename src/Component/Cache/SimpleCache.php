<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/4/17
 * Time:  12:04 上午
 */

namespace EasySwoole\Skeleton\Component\Cache;

use EasySwoole\Skeleton\Component\Cache\Driver\FileDriver;
use EasySwoole\Skeleton\Component\Cache\Packer\PhpSerializerPacker;
use Psr\SimpleCache\CacheInterface;

class SimpleCache implements CacheInterface
{

    protected $driver;

    public function __construct()
    {
        $cache = config('cache');
        $driver = $cache['driver'] ?? FileDriver::class;
        $packer = $cache['packer'] ?? PhpSerializerPacker::class;
        $this->driver = new $driver([
            'packer' => $packer,
            'prefix' => $cache['prefix'] ?? 'c:',
            'dir'    => $cache['dir'] ?? null,
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