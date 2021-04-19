<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/4/17
 * Time:  12:04 上午
 */

namespace EasySwoole\Skeleton\Component\Cache;

class SimpleCache
{
    /**
     * @var SimpleCacheConfig
     */
    protected $config;

    public function __construct(SimpleCacheConfig $config)
    {
        $this->config = $config;
    }
}