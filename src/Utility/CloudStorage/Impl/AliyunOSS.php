<?php
declare(strict_types=1);

namespace EasySwoole\Skeleton\Utility\CloudStorage\Impl;

use EasySwoole\EasySwoole\Logger;
use EasySwoole\Oss\AliYun\Config;
use EasySwoole\Oss\AliYun\Core\OssException;
use EasySwoole\Oss\AliYun\OssClient;
use EasySwoole\Skeleton\Errors\CommonError;
use EasySwoole\Skeleton\Framework\BizException;
use AlibabaCloud\Client\AlibabaCloud;
use EasySwoole\Skeleton\Helpers\DateHelper;
use EasySwoole\Skeleton\Utility\CloudStorage\AbstractCloudStorage;
use Throwable;

class AliyunOSS implements AbstractCloudStorage
{

    private $accessKeyID;

    private $accessKeySecret;

    private $endpoint;

    private $regionId;

    private $roleArn;

    private $roleSessionName;

    public function __construct()
    {
        $this->accessKeyID = config('thirdparty.aliyun.oss.access_key');
        $this->accessKeySecret = config('thirdparty.aliyun.oss.secret_key');
        $this->endpoint = config('thirdparty.aliyun.oss.endpoint');
        $this->regionId = config('thirdparty.aliyun.oss.regionId');

        $this->roleArn = config('thirdparty.aliyun.oss.roleArn');
        $this->roleSessionName = config('thirdparty.aliyun.oss.roleSessionName');

        try {
            AlibabaCloud::accessKeyClient($this->accessKeyID, $this->accessKeySecret)->regionId($this->regionId)->asDefaultClient();

        } catch (Throwable $e) {
            Logger::getInstance()->error($e->getMessage());
        }
    }

    public function getUploadToken($bucket, $key = null)
    {
        try {
            $result = AlibabaCloud::rpc()
                ->scheme('https')
                ->product('Sts')
                ->version('2015-04-01')
                ->action('AssumeRole')
                ->method('POST')
                ->host('sts.aliyuncs.com')
                ->options([
                    'query' => [
//                        'RegionId' => "oss-cn-shenzhen",
                        "RegionId"        => $this->regionId,
                        'RoleArn'         => $this->roleArn,
                        'RoleSessionName' => $this->roleSessionName,
                    ],
                ])
                ->request();
            $result = $result->toArray();
            return $result['Credentials'];
        } catch (Throwable $e) {
            Logger::getInstance()->error($e->getMessage());
            throw new BizException(CommonError::SERVER_ERROR);
        }
    }

    public function getSignUrl($bucket, $key, string $dir = '/')
    {
        $token = $this->getUploadToken($bucket, $key);

        $host = sprintf('https://%s.%s.aliyuncs.com', $bucket, $this->endpoint);

        $accessKeyId = $token['AccessKeyId'];
        $accessKeySecret = $token['AccessKeySecret'];

        $now = time();
        $expire = 30;
        $end = $now + $expire;
        $expiration = DateHelper::gmt_iso8601($end);

        //最大文件大小.用户可以自己设置
        $condition = [
            'content-length-range',
            0,
            1048576000,
        ];
        $conditions[] = $condition;

        // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
        $start = [
            'starts-with',
            '$key',
            $dir,
        ];
        $conditions[] = $start;

        $arr = [
            'expiration' => $expiration,
            'conditions' => $conditions,
        ];

        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $accessKeySecret, true));

        $response['accessid'] = $accessKeyId;
        $response['host'] = $host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['dir'] = $dir;  // 这个参数是设置用户上传文件时指定的前缀。
        $response['x-oss-security-token'] = $token['SecurityToken'];
        $response['success_action_status'] = '200';

        return $response;
    }

    public function uploadObject(string $bucket, string $key, string $content)
    {
        try {
            $config = new Config([
                'accessKeyId'     => $this->accessKeyID,
                'accessKeySecret' => $this->accessKeySecret,
                'endpoint'        => $this->endpoint,
            ]);
            $ossClient = new OssClient($config);
            return $ossClient->putObject($bucket, $key, $content);
        } catch (OssException $e) {
            Logger::getInstance()->error($e->getMessage());
            throw new BizException(CommonError::SERVER_ERROR);
        }
    }

    public function uploadFile($bucket, $object, $file, $options = NULL)
    {
        try {
            $config = new Config([
                'accessKeyId'     => $this->accessKeyID,
                'accessKeySecret' => $this->accessKeySecret,
                'endpoint'        => $this->endpoint,
            ]);
            $ossClient = new OssClient($config);
            return $ossClient->uploadFile($bucket, $object, $file, $options);
        } catch (OssException $e) {
            Logger::getInstance()->error($e->getMessage());
            throw new BizException(CommonError::SERVER_ERROR);
        }
    }

    public function getObject($bucket, $object, $options = NULL)
    {
        try {
            $config = new Config([
                'accessKeyId'     => $this->accessKeyID,
                'accessKeySecret' => $this->accessKeySecret,
                'endpoint'        => $this->endpoint,
            ]);
            $ossClient = new OssClient($config);
            return $ossClient->getObject($bucket, $object, $options);
        } catch (OssException $e) {
            Logger::getInstance()->error($e->getMessage());
            throw new BizException(CommonError::SERVER_ERROR);
        }
    }
}
