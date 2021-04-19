<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/4/17
 * Time:  12:01 上午
 */

namespace EasySwoole\Skeleton\Component\Cache\Driver;

use EasySwoole\Skeleton\BaseObject;
use EasySwoole\Skeleton\Component\Cache\Packer\PackerInterface;

class Driver extends BaseObject
{
    /**
     * @var string|null
     */
    protected $prefix;

    /**
     * @var PackerInterface
     */
    protected $packer;

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getCacheKey(string $key)
    {
        return $this->prefix . $key;
    }

    /**
     * @return string|null
     */
    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    /**
     * @param string|null $prefix
     *
     * @return Driver
     */
    public function setPrefix(?string $prefix): Driver
    {
        $this->prefix = $prefix;
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
     * @return Driver
     */
    public function setPacker(PackerInterface $packer): Driver
    {
        $this->packer = $packer;
        return $this;
    }
}