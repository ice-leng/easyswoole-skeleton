<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/5/20
 * Time:  8:29 下午
 */

namespace EasySwoole\Skeleton\Command;

use EasySwoole\Command\AbstractInterface\CommandHelpInterface;
use EasySwoole\Command\AbstractInterface\CommandInterface;
use EasySwoole\Command\Color;
use EasySwoole\Command\CommandManager;
use EasySwoole\Utility\FileSystem;

class VendorPublishCommand implements CommandInterface
{

    public $dependencyFileName = 'dependencies';

    public function commandName(): string
    {
        return 'vendor:publish';
    }

    public function help(CommandHelpInterface $commandHelp): CommandHelpInterface
    {
        $commandHelp->addAction('package', 'The package file you want to publish.');
        $commandHelp->addActionOpt('id', 'The id of the package you want to publish.');
        $commandHelp->addActionOpt('show', 'Show all packages can be publish.');
        $commandHelp->addActionOpt('force', 'Overwrite any existing files');
        return $commandHelp;
    }

    public function desc(): string
    {
        return 'Publish any publishable configs from vendor packages.';
    }

    public function exec(): ?string
    {
        $fileSystem = new FileSystem();

        $package = CommandManager::getInstance()->getArg('package');
        $force = CommandManager::getInstance()->getOpt('force', 'false');
        $show = CommandManager::getInstance()->getOpt('show', 'false');
        $id = CommandManager::getInstance()->getOpt('database');

//        echo Color::info("Migration table created successfully.");

        var_dump($package, $force, $show, $id);
    }
}