<?php

namespace EasySwoole\Skeleton\Helpers;

class RegularHelper
{
    /**
     * 正则
     *
     * @param string $url
     *
     * @return bool
     * @author lengbin(lengbin0@gmail.com)
     */
    public static function isInvalidUrl(string $url): bool
    {
        if (StringHelper::isEmpty($url)) {
            return true;
        }
        $rule = "/((http|https):\/\/)+(\w+)[\w\/\.\-]*/";
        return !preg_match($rule, $url);
    }

    /**
     * 正则
     *
     * @param string $url
     *
     * @return bool
     * @author lengbin(lengbin0@gmail.com)
     */
    public static function isInvalidImage($url): bool
    {
        if (StringHelper::isEmpty($url)) {
            return true;
        }
        $rule = "/((http|https):\/\/)?\w+\.(jpg|jpeg|gif|png)/";
        return !preg_match($rule, $url);
    }

    /**
     * 密码
     *
     * @param $password
     *
     * @return bool
     */
    public static function isInvalidPassword($password): bool
    {
        if (StringHelper::isEmpty($password)) {
            return true;
        }
        $rule = '/^(?=.*[a-zA-Z0-9].*)(?=.*[a-zA-Z\W].*)(?=.*[0-9\W].*).{6,20}$/';
        return !preg_match($rule, $password);
    }

    /**
     * 手机号
     *
     * @param $mobile
     *
     * @return bool
     */
    public static function isInvalidMobile($mobile): bool
    {
        if (StringHelper::isEmpty($mobile)) {
            return true;
        }
        if (strpos($mobile, '+86') === 0) {
            $mobile = substr($mobile, 3);
        }
        $rule = '/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/';
        return !preg_match($rule, $mobile);
    }
}
