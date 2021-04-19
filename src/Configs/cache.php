<?php

declare(strict_types=1);

use EasySwoole\Skeleton\Component\Cache\Driver\FileDriver;
use EasySwoole\Skeleton\Component\Cache\Packer\PhpSerializerPacker;

return [
    'driver' => FileDriver::class,
    'packer' => PhpSerializerPacker::class,
    'prefix' => 'c:',
    'dir'    => EASYSWOOLE_ROOT . '/runtime/cache'
];
