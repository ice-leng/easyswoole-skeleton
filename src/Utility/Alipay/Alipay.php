<?php

namespace EasySwoole\Skeleton\Utility\Alipay;

use Alipay\EasySDK\Base\OAuth\Models\AlipaySystemOauthTokenResponse;
use Alipay\EasySDK\Kernel\Config;
use Alipay\EasySDK\Kernel\Factory;
use Throwable;

class Alipay
{

    public function __construct()
    {
        $options = new Config();
        $options->appId = config('thirdparty.aliyun.app_id');
        $options->merchantPrivateKey = config('thirdparty.aliyun.privateKey');
        $options->alipayPublicKey = config('thirdparty.aliyun.publicKey');
        $options->protocol = 'https';
        $options->gatewayHost = 'openapi.alipay.com';
        $options->signType = 'RSA2';
        Factory::setOptions($options);
    }


    /**
     * @param $authCode
     *
     * @return AlipaySystemOauthTokenResponse
     * @throws Throwable
     */
    public function codeToUser($authCode) :AlipaySystemOauthTokenResponse
    {
        return Factory::base()->oauth()->getToken($authCode);
    }

}
