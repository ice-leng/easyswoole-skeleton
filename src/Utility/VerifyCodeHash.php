<?php

namespace EasySwoole\Skeleton\Utility;

class VerifyCodeHash
{
    const DURATION = 2 * 60;

    /**
     * @param $code
     * @param $time
     * @param $hash
     *
     * @return bool
     */
    public static function checkVerifyCode($code, $time, $hash): bool
    {
        if ($time + self::DURATION < time()) {
            return false;
        }
        $code = strtolower($code);
        return self::getVerifyCodeHash($code, $time) == $hash;
    }

    /**
     * @param $code
     * @param $time
     *
     * @return string
     */
    public static function getVerifyCodeHash($code, $time): string
    {
        return md5($code . $time);
    }
}