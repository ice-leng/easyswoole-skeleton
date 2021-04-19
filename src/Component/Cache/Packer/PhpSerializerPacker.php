<?php

declare(strict_types=1);

namespace EasySwoole\Skeleton\Component\Cache\Packer;

class PhpSerializerPacker implements PackerInterface
{
    public function pack($data): string
    {
        return serialize($data);
    }

    public function unpack(string $data)
    {
        return unserialize($data);
    }
}
