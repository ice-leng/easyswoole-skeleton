<?php
return [
    'host'   => '127.0.0.1',
    'port'   => '6379',
    'auth'   => '',
    'ttl'    => 3600,
    'random' => [1, 60],

    'intervalCheckTime' => 30 * 1000,
    'maxIdleTime'       => 15,
    'maxObjectNum'      => 60,
    'minObjectNum'      => 5,
    'getObjectTimeout'  => 3,
];
