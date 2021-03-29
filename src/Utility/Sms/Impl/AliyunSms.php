<?php
declare(strict_types=1);

namespace EasySwoole\Skeleton\Utility\Sms\Impl;

use EasySwoole\EasySwoole\Logger;
use EasySwoole\Skeleton\Errors\CommonError;
use EasySwoole\Skeleton\Framework\BizException;
use EasySwoole\Skeleton\Utility\Sms\Sms;
use AlibabaCloud\Client\AlibabaCloud;
use Throwable;

class AliyunSms implements Sms
{
    private $accessKeyID;

    private $accessKeySecret;

    public function __construct()
    {
        $this->accessKeyID = config('thirdparty.aliyun.access_key');
        $this->accessKeySecret = config('thirdparty.aliyun.secret_key');

        try {
            AlibabaCloud::accessKeyClient($this->accessKeyID, $this->accessKeySecret)
                ->regionId('cn-shenzhen')
                ->asDefaultClient();
        } catch (Throwable $e) {
            Logger::getInstance()->error($e->getMessage());
            throw new BizException(CommonError::SERVER_ERROR);
        }
    }

    public function sendSms($mobile, $sign, $template, $content)
    {
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->scheme('https')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-shenzhen",
                        'PhoneNumbers' => $mobile,
                        'SignName' => $sign,
                        'TemplateCode' => $template,
                        'TemplateParam' => json_encode($content),
                    ],
                ])
                ->request();

            return $result->toArray();
        } catch (Throwable $e) {
            Logger::getInstance()->error($e->getMessage());
            throw new BizException(CommonError::SERVER_ERROR);
        }
    }
}