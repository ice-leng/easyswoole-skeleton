<?php
/**
 * User: ice
 * Date: 2021/2/23 18:11
 * Author: ice <xykxyk2008@163.com>
 */

namespace EasySwoole\Skeleton\Command;

use EasySwoole\Command\AbstractInterface\CommandHelpInterface;
use EasySwoole\Command\AbstractInterface\CommandInterface;
use EasySwoole\Command\Color;
use EasySwoole\Skeleton\Utility\ErrorCode\MergeErrorCode;
use EasySwoole\EasySwoole\Core;

class ErrorCodeCommand implements CommandInterface
{
    public function commandName(): string
    {
        return 'gen:error';
    }

    public function exec(): ?string
    {
        try {
            Core::getInstance()->initialize();
            $config = config('errorCode', []);
            $mergeErrorCode = new MergeErrorCode($config);
            $status = $mergeErrorCode->generate();
            return $status ? Color::info('success') : Color::error('false: generate fail');
        }catch (\Exception $exception) {
            return Color::error("false: {$exception->getMessage()}");
        }
    }

    public function help(CommandHelpInterface $commandHelp): CommandHelpInterface
    {
        return $commandHelp;
    }

    // 设置自定义命令描述
    public function desc(): string
    {
        return 'merge error code';
    }
}
