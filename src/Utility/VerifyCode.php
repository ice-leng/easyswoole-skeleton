<?php

namespace EasySwoole\Skeleton\Utility;

class VerifyCode
{
    const DURATION = 5 * 60;

    static function checkVerifyCode($code, $time, $hash)
    {
        if ($time + self::DURATION < time()) {
            return false;
        }
        $code = strtolower($code);
        return self::getVerifyCodeHash($code, $time) == $hash;
    }

    static function getVerifyCodeHash($code, $time)
    {
        return md5($code . $time);
    }
}