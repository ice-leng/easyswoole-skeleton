<?php
declare(strict_types=1);

namespace EasySwoole\Skeleton\Framework;

use EasySwoole\Skeleton\Utility\AbstractEnum;

class BaseEnum extends AbstractEnum
{
    public function getMessage(array $replace = []): string
    {
        // todo  多语言 翻译
        return parent::getMessage($replace);
    }
}
