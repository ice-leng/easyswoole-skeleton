<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/3/29
 * Time:  2:54 下午
 */

return [
    'wechat'  => [
        // 小程序
        'app_id'         => '',
        'app_secret'     => '',
        // 公总号
        'appId'          => '',
        'appSecret'      => '',
        // 商户号
        'mch_id'         => '',
        'key'            => '',
        'notifyUrl'      => '',
        'apiclient_cert' => '',
        'apiclient_key'  => '',
    ],
    'aliyun'  => [
        'app_id'     => '',
        'privateKey' => '',
        'publicKey'  => '',
        'oss'        => [
            'access_key'      => '',
            'secret_key'      => '',
            'bucket'          => '',
            'endpoint'        => 'oss-cn-shenzhen.aliyuncs.com',
            'regionId'        => 'cn-shenzhen',
            'url'             => "",
            'roleArn'         => 'acs:ram::xxxx',
            'roleSessionName' => 'external-username',
        ],
        'sms'        => [
            'sign'     => '',
            'template' => [],
        ],
    ],
    'qiniu'   => [
        'oss' => [
            'access_key' => '',
            'secret_key' => '',
            'bucket'     => '',
            'url'        => '',
        ],
    ],
    'tencent' => [
        'oss' => [
            'url'             => 'https://sts.tencentcloudapi.com/',
            'domain'          => 'sts.tencentcloudapi.com',
            'secretId'        => '',
            'secretKey'       => '',
            'durationSeconds' => 1800,
            'bucket'          => '',
            'region'          => '',
            'allowPrefix'     => '*',
            'allowActions'    => [
                // 简单上传
                "name/cos:PutObject",
                // 表单上传、小程序上传
                "name/cos:PostObject",
                // 分片上传
                "name/cos:InitiateMultipartUpload",
                "name/cos:ListMultipartUploads",
                "name/cos:ListParts",
                "name/cos:UploadPart",
                "name/cos:CompleteMultipartUpload",
            ],
        ],
    ],
    'local'   => [
        'url'              => '',
        'url_name'         => 'localhost',
        'bucket'           => '/public/upload',
        'upload_time'      => 5,
        'remove_file_time' => 3600 * 24,
    ],
];
