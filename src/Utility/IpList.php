<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/4/20
 * Time:  12:22 上午
 */

namespace EasySwoole\Skeleton\Utility;

use EasySwoole\Component\Singleton;
use EasySwoole\Component\TableManager;
use Swoole\Table;

class IpList
{
    use Singleton;

    /** @var Table */
    protected $table;

    public function __construct()
    {
        TableManager::getInstance()->add('ipList', [
            'ip'             => [
                'type' => Table::TYPE_STRING,
                'size' => 16,
            ],
            'count'          => [
                'type' => Table::TYPE_INT,
                'size' => 8,
            ],
            'lastAccessTime' => [
                'type' => Table::TYPE_INT,
                'size' => 8,
            ],
        ], 1024 * 128);
        $this->table = TableManager::getInstance()->get('ipList');
    }

    public function access(string $ip): int
    {
        $key = substr(md5($ip), 8, 16);
        $info = $this->table->get($key);

        if ($info) {
            $this->table->set($key, [
                'lastAccessTime' => time(),
                'count'          => $info['count'] + 1,
            ]);
            return $info['count'] + 1;
        } else {
            $this->table->set($key, [
                'ip'             => $ip,
                'lastAccessTime' => time(),
                'count'          => 1,
            ]);
            return 1;
        }
    }

    public function clear()
    {
        foreach ($this->table as $key => $item) {
            $this->table->del($key);
        }
    }

    public function accessList($count = 10): array
    {
        $ret = [];
        foreach ($this->table as $key => $item) {
            if ($item['count'] >= $count) {
                $ret[] = $item;
            }
        }
        return $ret;
    }
}