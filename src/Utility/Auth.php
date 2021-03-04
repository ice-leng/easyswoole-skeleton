<?php

namespace EasySwoole\Skeleton\Utility;

use EasySwoole\Jwt\Exception;
use EasySwoole\Jwt\Jwt;
use EasySwoole\Jwt\JwtObject;
use EasySwoole\Redis\Exception\RedisException;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Skeleton\Framework\BizException;
use EasySwoole\Utility\SnowFlake;
use EasySwoole\Skeleton\Helpers\Arrays\ArrayHelper;
use EasySwoole\Skeleton\Errors\CommonError;

/**
 * 当前只实现了 多端登录
 *
 * 单点登录 未实现
 *
 * Class Auth
 * @package EasySwoole\Skeleton\Component
 */
class Auth
{
    protected $config;

    public function __construct(array $config = [])
    {
        if (empty($config)) {
            $config = config('jwt', []);
        }
        $this->config = $config;
    }

    /**
     * @return Jwt
     */
    protected function getJwt(): Jwt
    {
        $key = ArrayHelper::getValue($this->config, 'key', 'easyswoole');
        return Jwt::getInstance()->setSecretKey($key);
    }

    /**
     * 生成token
     *
     * @param array $data
     *
     * @return string
     */
    public function generate(array $data): string
    {
        $time = time();
        $alg = ArrayHelper::get($this->config, 'alg', 'HMACSHA256');
        $exp = ArrayHelper::get($this->config, 'exp', (2 * 3600));
        $json = json_encode($data);
        $jwtObject = $this->getJwt()->publish();
        $jwtObject->setAlg($alg)->setExp(($time + $exp))->setIat($time)->setJti(md5($time))->setData($json);
        return $jwtObject->__toString();
    }

    /**
     * 生成 刷新token
     *
     * @param string $token
     *
     * @return string
     * @throws RedisException|BizException
     */
    public function generateRefreshToken(string $token): string
    {
        $redis = RedisPool::defer('redis');
        $ttl = ArrayHelper::get($this->config, 'ttl', (24 * 3600 * 30));
        $refreshToken = (string)SnowFlake::make(1, 1);
        $data = $this->verify($token)->getData();
        $redis->set($refreshToken, $data, $ttl);
        // token 与  刷新token 关联
        $exp = ArrayHelper::get($this->config, 'exp', (2 * 3600));
        $redis->set($token, $refreshToken, $exp);
        return $refreshToken;
    }

    /**
     * 获得 JwtObject
     *
     * @param string $token
     *
     * @return JwtObject|null
     * @throws BizException
     */
    private function verify(string $token): ?JwtObject
    {
        try {
            $jwtObject = $this->getJwt()->decode($token);
            $status = $jwtObject->getStatus();
            // 无效
            if ($status === -1) {
                throw new BizException(CommonError::INVALID_TOKEN);
            }
            // 过期
            if ($status === -2) {
                throw new BizException(CommonError::TOKEN_EXPIRED);
            }
            return $jwtObject;
        } catch (Exception $e) {
            throw new BizException(CommonError::INVALID_TOKEN);
        }
    }

    /**
     * @param string $token
     *
     * @return array|null
     * @throws BizException
     */
    public function verifyToken(string $token): ?array
    {
        $data = $this->verify($token)->getData();
        $redis = RedisPool::defer('redis');
        // 判断 token 是否 和 刷新token 关联
        $refreshToken = $redis->get($token);
        if (empty($refreshToken)) {
            throw new BizException(CommonError::INVALID_TOKEN);
        }
        // 如果没有 表示 退出了
        $result = $redis->get($refreshToken);
        if (empty($result)) {
            throw new BizException(CommonError::INVALID_TOKEN);
        }
        return json_decode($data, true);
    }

    /**
     * 刷新token
     *
     * @param string $refreshToken
     *
     * @return string
     * @throws BizException|RedisException
     */
    public function refreshToken(string $refreshToken): string
    {
        $redis = RedisPool::defer('redis');
        $data = $redis->get($refreshToken);
        if (empty($data)) {
            throw new BizException(CommonError::TOKEN_EXPIRED);
        }
        $data = json_decode($data, true);
        if (empty($data)) {
            throw new BizException(CommonError::TOKEN_EXPIRED);
        }

        $token = $this->generate($data);
        // token 与  刷新token 关联
        $exp = ArrayHelper::get($this->config, 'exp', (2 * 3600));
        $redis->set($token, $refreshToken, $exp);
        return $token;
    }

    /**
     * 注销
     *
     * @param string $token
     *
     * @return bool
     */
    public function logout(string $token): bool
    {
        $delete = [$token];
        $redis = RedisPool::defer('redis');
        $refreshToken = $redis->get($token);
        if (!empty($refreshToken)) {
            $delete[] = $refreshToken;
        }
        $redis->del(...$delete);
        return true;
    }

}
