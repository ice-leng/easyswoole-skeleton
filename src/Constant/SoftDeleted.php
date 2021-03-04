<?php

declare(strict_types=1);

namespace EasySwoole\Skeleton\Constant;

use EasySwoole\Skeleton\Framework\BaseEnum;

/**
 * 软删除状态
 */
class SoftDeleted extends BaseEnum
{
    /**
     * @Message("正常")
     */
    const ENABLE = 1;

    /**
     * @Message("删除")
     */
    const DISABLE = 2;
}
