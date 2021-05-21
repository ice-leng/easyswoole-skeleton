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
use Lengbin\Helper\YiiSoft\VarDumper;

class VendorPublishCommand implements CommandInterface
{

    /**
     * @var FileSystem
     */
    protected $fileSystem;

    public function __construct()
    {
        $this->fileSystem = new FileSystem();
    }

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
        $commandHelp->addActionOpt('dependencyPath', 'The dependency merge file path.');

        return $commandHelp;
    }

    public function desc(): string
    {
        return 'Publish any publishable configs from vendor packages.';
    }

    public function exec(): ?string
    {
        $dependencyPath = CommandManager::getInstance()->getOpt('dependencyPath', EASYSWOOLE_ROOT . '/App/Configs/dependencies.php');
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

        $dependencies = ArrayHelper::get($config, 'dependencies', []);
        $this->merge($dependencies, $dependencyPath);

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

    protected function merge(array $dependencies, string $file)
    {
        if (!$this->fileSystem->isDirectory($dirname = dirname($file))) {
            $this->fileSystem->makeDirectory($dirname, 0755, true);
        }

        if (is_file($file)) {
            $customDependencies = include $file;
            $dependencies = array_merge($dependencies, $customDependencies);
        }
        $this->fileSystem->put($file, VarDumper::export($dependencies));
        echo Color::green("dependencies import successfully.");
    }

    protected function copy($package, $items, $force)
    {
        foreach ($items as $item) {
            if (!isset($item['id'], $item['source'], $item['destination'])) {
                continue;
            }

            $id = $item['id'];
            $source = $item['source'];
            $destination = $item['destination'];

            if (!$force && $this->fileSystem->exists($destination)) {
                echo Color::red(sprintf('[%s] already exists.', $destination));
                continue;
            }

            if (!$this->fileSystem->isDirectory($dirname = dirname($destination))) {
                $this->fileSystem->makeDirectory($dirname, 0755, true);
            }

            if ($this->fileSystem->isDirectory($source)) {
                $this->fileSystem->copyDirectory($source, $destination);
            } else {
                $this->fileSystem->copy($source, $destination);
            }

            echo Color::green(sprintf('[%s] publishes [%s] successfully.', $package, $id));
        }
        return '';
    }
}