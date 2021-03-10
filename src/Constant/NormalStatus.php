<?php
declare(strict_types=1);

namespace EasySwoole\Skeleton\Constant;

use EasySwoole\Skeleton\Framework\BaseEnum;

class NormalStatus extends BaseEnum
{
    /**
     * @Message("正常")
     */
    const NORMAL = 1;

    /**
     * @Message("冻结")
     */
    const FROZEN = 2;
}
