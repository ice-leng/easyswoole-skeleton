<?php
//全局bootstrap事件
use EasySwoole\HyperfOrm\CommandUtility;
use EasySwoole\Skeleton\Command\ErrorCodeCommand;
use EasySwoole\Skeleton\Command\VendorPublishCommand;

// command
CommandUtility::getInstance()->init([
    new ErrorCodeCommand(),
    new VendorPublishCommand(),
]);

