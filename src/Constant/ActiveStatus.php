<?php

namespace EasySwoole\Skeleton\Constant;

use EasySwoole\Skeleton\Framework\BaseEnum;

class ActiveStatus extends BaseEnum
{
    /**
     * @Message("启用")
     */
    const ENABLE = 1;

    /**
     * @Message("未启用")
     */
    const DISABLE = 2;
}