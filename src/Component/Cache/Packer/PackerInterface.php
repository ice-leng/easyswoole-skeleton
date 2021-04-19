<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/4/17
 * Time:  12:00 上午
 */

namespace EasySwoole\Skeleton\Component\Cache\Packer;

interface PackerInterface
{
    public function pack($data): string;

    public function unpack(string $data);

}