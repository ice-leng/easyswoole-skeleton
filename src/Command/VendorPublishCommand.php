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
use EasySwoole\Skeleton\Helpers\Arrays\ArrayHelper;
use EasySwoole\Skeleton\Utility\Composer;
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

        $package = CommandManager::getInstance()->getArg('package', ArrayHelper::get(CommandManager::getInstance()->getOriginArgv(), 2));
        $force = CommandManager::getInstance()->getOpt('force', false);
        $show = CommandManager::getInstance()->getOpt('show', false);
        $id = CommandManager::getInstance()->getOpt('database');

        $extra = Composer::getMergedExtra()[$package] ?? null;
        if (empty($extra)) {
            return Color::danger(sprintf('package [%s] misses `extra` field in composer.json.', $package));
        }

        $provider = ArrayHelper::get($extra, 'easyswoole.config');
        $config = (new $provider())();

        $publish = ArrayHelper::get($config, 'publish');
        if (empty($publish)) {
            return Color::danger(sprintf('No file can be published from package [%s].', $package));
        }

        if ($show) {
            foreach ($publish as $item) {
                $out = '';
                foreach ($item as $key => $value) {
                    $out .= sprintf('%s: %s', $key, $value) . PHP_EOL;
                }
                Color::green($out);
            }
            return '';
        }

        if ($id) {
            $item = (array_filter($publish, function ($item) use ($id) {
                return $item['id'] == $id;
            }, ARRAY_FILTER_USE_BOTH));

            if (empty($item)) {
                return Color::red(sprintf('No file can be published from [%s].', $id));
            }

            return $this->copy($package, $item, $force);
        }

        return $this->copy($package, $publish, $force);
    }

    protected function copy($package, $items, $force)
    {
        $fileSystem = new FileSystem();
        foreach ($items as $item) {
            if (! isset($item['id'], $item['source'], $item['destination'])) {
                continue;
            }

            $id = $item['id'];
            $source = $item['source'];
            $destination = $item['destination'];

            if (! $force && $fileSystem->exists($destination)) {
                echo Color::red(sprintf('[%s] already exists.', $destination));
                continue;
            }

            if (! $fileSystem->isDirectory($dirname = dirname($destination))) {
                $fileSystem->makeDirectory($dirname, 0755, true);
            }

            if ($fileSystem->isDirectory($source)) {
                $fileSystem->copyDirectory($source, $destination);
            } else {
                $fileSystem->copy($source, $destination);
            }

            echo Color::green(sprintf('[%s] publishes [%s] successfully.', $package, $id));
        }
        return '';
    }
}