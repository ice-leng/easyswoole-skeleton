<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/4/20
 * Time:  12:25 上午
 */

namespace EasySwoole\Skeleton\Process;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Skeleton\Utility\IpList;

class IpAccessCountProcess extends AbstractProcess
{
    protected function run($arg)
    {
        $this->addTick(10 * 1000, function () {
            /**
             * 正常用户不会有一秒超过 6 次的api请求
             * 做列表记录并清空
             */
            IpList::getInstance()->accessList(30);
            IpList::getInstance()->clear();
        });
    }
}