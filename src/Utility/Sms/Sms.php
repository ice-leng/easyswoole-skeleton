<?php
declare(strict_types=1);


namespace EasySwoole\Skeleton\Utility\Sms;


interface Sms
{
    public function sendSms($mobile, $sign, $template, $content);
}