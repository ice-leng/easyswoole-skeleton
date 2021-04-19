<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/4/17
 * Time:  12:05 上午
 */

namespace EasySwoole\Skeleton\Component\Cache;

use EasySwoole\Skeleton\BaseObject;
use Hyperf\Contract\PackerInterface;
use Psr\SimpleCache\CacheInterface;

class SimpleCacheConfig extends BaseObject
{

    /**
     * @var CacheInterface
     */
    protected $driver;

    /**
     * @var PackerInterface
     */
    protected $packer;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var null|string
     */
    protected $dir = null;

    /**
     * @return CacheInterface
     */
    public function getDriver(): CacheInterface
    {
        return $this->driver;
    }

    /**
     * @param CacheInterface $driver
     *
     * @return SimpleCacheConfig
     */
    public function setDriver(CacheInterface $driver): SimpleCacheConfig
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @return PackerInterface
     */
    public function getPacker(): PackerInterface
    {
        return $this->packer;
    }

    /**
     * @param PackerInterface $packer
     *
     * @return SimpleCacheConfig
     */
    public function setPacker(PackerInterface $packer): SimpleCacheConfig
    {
        $this->packer = $packer;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     *
     * @return SimpleCacheConfig
     */
    public function setPrefix(string $prefix): SimpleCacheConfig
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDir(): ?string
    {
        return $this->dir;
    }

    /**
     * @param string|null $dir
     *
     * @return SimpleCacheConfig
     */
    public function setDir(?string $dir): SimpleCacheConfig
    {
        $this->dir = $dir;
        return $this;
    }
}