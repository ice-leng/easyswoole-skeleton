<?php
declare(strict_types=1);

namespace EasySwoole\Skeleton\Framework;

use EasySwoole\Skeleton\Utility\AbstractEnum;

class BaseEnum extends AbstractEnum
{
    public function getMessage(array $replace = []): string
    {
        $message = parent::getMessage($replace);
        if (function_exists('__')) {
            $message = __($message, $replace);
        }
        return $message;
    }
}
