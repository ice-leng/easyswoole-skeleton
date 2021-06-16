<?php
declare(strict_types=1);

use EasySwoole\EasySwoole\Config;

if (!function_exists('config')) {
    function config(string $key, $default = null) {
        $config = Config::getInstance()->getConf($key);
        return $config ?? $default;
    }
}

if (!function_exists('isOnline')) {
    function isOnline() {
        $env = Config::getInstance()->getConf('app.env');
        return $env && strtolower($env) === 'online';
    }
}

if (! function_exists('format_throwable')) {
    /**
     * Format a throwable to string.
     * @param Throwable $throwable
     * @return string
     */
    function format_throwable(Throwable $throwable): string
    {
        return sprintf(
            "%s:%s(%s) in %s:%s\nStack trace:\n%s",
            get_class($throwable),
            $throwable->getMessage(),
            $throwable->getCode(),
            $throwable->getFile(),
            $throwable->getLine(),
            $throwable->getTraceAsString()
        );
    }
}
