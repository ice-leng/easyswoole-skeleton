<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/6/19
 * Time:  8:57 下午
 */

namespace EasySwoole\Skeleton\Component\Menu;

interface PermissionInterface
{
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getPermissionByKey(string $key);
}
