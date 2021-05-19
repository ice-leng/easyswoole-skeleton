<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace EasySwoole\Skeleton;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'publish' => [
                [
                    'id' => 'skeleton',
                    'description' => 'The config for skeleton.',
                    'source' => __DIR__ . '/Configs/jwt.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/Configs/jwt2.php',
                ],
            ],
        ];
    }
}
