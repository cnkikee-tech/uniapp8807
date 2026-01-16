<?php
declare(strict_types=1);

namespace app\common\utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\facade\Config;
use think\facade\Cache;

/**
 * JWT工具类
 */
class JwtUtil
{
    /**
     * 生成JWT Token
     * @param array $payload
     * @return string
     */
    public static function encode(array $payload): string
    {
        $config = Config::get('jwt');
        
        // 添加标准声明
        $payload = array_merge($payload, [
            'iss' => $config['issuer'],
            'aud' => $config['audience'],
            'sub' => $config['subject'],
            'iat' => time(),
            'exp' => time() + $config['expire'],
            'nbf' => time(),
            'jti' => md5(uniqid('JWT') . time())
        ]);

        return JWT::encode($payload, $config['secret'], $config['algorithm']);
    }

    /**
     * 解析JWT Token
     * @param string $token
     * @return array
     */
    public static function decode(string $token): array
    {
        $config = Config::get('jwt');
        
        try {
            $decoded = JWT::decode($token, new Key($config['secret'], $config['algorithm']));
            return (array) $decoded;
        } catch (\Exception $e) {
            throw new \Exception('Token解析失败: ' . $e->getMessage());
        }
    }

    /**
     * 验证JWT Token
     * @param string $token
     * @return bool
     */
    public static function verify(string $token): bool
    {
        try {
            self::decode($token);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取Token中的用户信息
     * @param string $token
     * @return array
     */
    public static function getUserInfo(string $token): array
    {
        $payload = self::decode($token);
        
        return [
            'id' => $payload['user_id'] ?? 0,
            'username' => $payload['username'] ?? '',
            'exp' => $payload['exp'] ?? 0
        ];
    }

    /**
     * 刷新JWT Token
     * @param string $token
     * @return string
     */
    public static function refresh(string $token): string
    {
        $payload = self::decode($token);
        
        // 移除过期时间等字段
        unset($payload['iat'], $payload['exp'], $payload['nbf'], $payload['jti']);
        
        return self::encode($payload);
    }

    /**
     * 将Token加入黑名单
     * @param string $token
     * @return bool
     */
    public static function addToBlacklist(string $token): bool
    {
        $payload = self::decode($token);
        $jti = $payload['jti'] ?? '';
        $exp = $payload['exp'] ?? 0;
        
        if ($jti && $exp > time()) {
            $cacheKey = 'jwt_blacklist:' . $jti;
            $ttl = $exp - time();
            Cache::set($cacheKey, true, $ttl);
            return true;
        }
        
        return false;
    }

    /**
     * 检查Token是否在黑名单中
     * @param string $token
     * @return bool
     */
    public static function isBlacklisted(string $token): bool
    {
        try {
            $payload = self::decode($token);
            $jti = $payload['jti'] ?? '';
            
            if ($jti) {
                $cacheKey = 'jwt_blacklist:' . $jti;
                return (bool) Cache::get($cacheKey);
            }
        } catch (\Exception $e) {
            return true;
        }
        
        return false;
    }
}