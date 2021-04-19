<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/4/16
 * Time:  11:58 下午
 */

namespace EasySwoole\Skeleton\Component\Cache\Driver;

use EasySwoole\Utility\File;
use EasySwoole\Utility\FileSystem;
use Exception;
use Psr\SimpleCache\CacheInterface;

class FileDriver extends Driver implements CacheInterface
{
    /**
     * @var string|null
     */
    protected $dir;

    /**
     * @var FileSystem
     */
    protected $fileSystem;

    public function init()
    {
        if (is_null($this->dir)) {
            $this->dir = EASYSWOOLE_ROOT . '/Cache';
        }
        $this->fileSystem = new FileSystem();
        File::createDirectory($this->dir);
        $this->prefix = $this->prefix ?? 'cache:';
    }

    /**
     * @return string
     */
    protected function getPrefix(): string
    {
        return $this->dir . DIRECTORY_SEPARATOR . $this->prefix;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getCacheKey(string $key)
    {
        return $this->getPrefix() . $key . '.cache';
    }

    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     * @throws Exception
     */
    public function get($key, $default = null)
    {
        $file = $this->getCacheKey($key);
        if ($this->fileSystem->missing($file)) {
            return $default;
        }
        if ($this->fileSystem->lastModified($file) < time()) {
            return $default;
        }
        return $this->packer->unpack($this->fileSystem->get($file));
    }

    public function getTtlTime($ttl = null)
    {
        // 如果不设置时间 默认 100 年
        if (is_null($ttl)) {
            $ttl = 3600 * 24 * 30 * 12 * 100;
        }
        $ttl = $ttl + time();
        return $ttl;
    }

    public function set($key, $value, $ttl = null)
    {
        $file = $this->getCacheKey($key);
        $this->fileSystem->put($file, $this->packer->pack($value));
        if ($ttl < time()) {
            $ttl = $this->getTtlTime($ttl);
        }
        return touch($file, $ttl);
    }

    public function delete($key)
    {
        $file = $this->getCacheKey($key);
        return $this->fileSystem->delete($file);
    }

    public function clear()
    {
        $files = glob($this->getPrefix() . '*');
        foreach ($files as $file) {
            if (is_dir($file)) {
                continue;
            }
            unlink($file);
        }
    }

    public function getMultiple($keys, $default = null)
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $result = [];
        foreach ($keys as $i => $key) {
            $result[$key] = $this->get($key, $default);
        }
        return $result;
    }

    public function setMultiple($values, $ttl = null)
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        $ttl = $this->getTtlTime($ttl);
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }

    public function deleteMultiple($keys)
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }

        foreach ($keys as $index => $key) {
            $this->delete($key);
        }

        return true;
    }

    public function has($key)
    {
        $file = $this->getCacheKey($key);
        return file_exists($file);
    }
}