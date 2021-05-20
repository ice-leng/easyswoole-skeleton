<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/3/29
 * Time:  2:54 下午
 */

return [
    'wechat' => [
        // 小程序
        'app_id'     => '',
        'app_secret' => '',
        // 公总号
        'appId'      => '',
        'appSecret'  => '',
        // 商户号
        'mch_id'         => '',
        'key'            => '',
        'notifyUrl'      => '',
        'apiclient_cert' => '',
        'apiclient_key'  => '',
    ],
    'aliyun' => [
        'app_id'          => '',
        'privateKey'      => '',
        'publicKey'       => '',
        'oss' => [
            'access_key'      => '',
            'secret_key'      => '',
            'bucket'          => '',
            'endpoint'        => 'oss-cn-shenzhen.aliyuncs.com',
            'regionId'        => 'cn-shenzhen',
            'url'             => "",
            'roleArn'         => 'acs:ram::xxxx',
            'roleSessionName' => 'external-username',
        ],
        'sms' => [
            'sign'     => '',
            'template' => [],
        ]
    ],
    'qiniu'    => [
        'access_key' => '',
        'secret_key' => '',
        'bucket'     => '',
        'url'        => '',
    ],
];