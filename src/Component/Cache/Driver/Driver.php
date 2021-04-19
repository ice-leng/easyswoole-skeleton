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
}