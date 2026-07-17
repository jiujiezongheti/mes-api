<?php

namespace app\common\utils;

use app\common\exceptions\BusinessException;
use app\common\ResponseCode;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtUtil
{
    public static function generate(string $type, int $userId): array
    {
        $config = config("jwt.{$type}");
        $now = time();

        $payload = [
            'iss' => $config['iss'],
            'iat' => $now,
            'exp' => $now + $config['expire'],
            'refresh_exp' => $now + $config['refresh_expire'],
            'user_id' => $userId,
            'type' => $type,
        ];

        $token = JWT::encode($payload, $config['secret'], 'HS256');

        return [
            'token' => $token,
            'exp' => $payload['exp'],
            'refresh_exp' => $payload['refresh_exp'],
        ];
    }

    public static function decode(string $token): object
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new BusinessException('登录已过期，请重新登录', ResponseCode::ERROR_TOKEN_EXPIRED->value);
        }

        $payload = json_decode(JWT::urlsafeB64Decode($parts[1]));
        if (!$payload || empty($payload->type)) {
            throw new BusinessException('登录已过期，请重新登录', ResponseCode::ERROR_TOKEN_EXPIRED->value);
        }

        return $payload;
    }

    public static function verify(string $token, string $type): object
    {
        $payload = self::decode($token);

        if ($payload->type !== $type) {
            throw new BusinessException('请先登录', ResponseCode::ERROR_AUTH->value);
        }

        $config = config("jwt.{$type}");

        try {
            return JWT::decode($token, new Key($config['secret'], 'HS256'));
        } catch (\Exception $e) {
            throw new BusinessException('登录已过期，请重新登录', ResponseCode::ERROR_TOKEN_EXPIRED->value);
        }
    }

    public static function refresh(string $token, string $type): array
    {
        $payload = self::decode($token);

        if ($payload->type !== $type) {
            throw new BusinessException('请先登录', ResponseCode::ERROR_AUTH->value);
        }

        if ($payload->refresh_exp < time()) {
            throw new BusinessException('登录已过期，请重新登录', ResponseCode::ERROR_TOKEN_EXPIRED->value);
        }

        $config = config("jwt.{$type}");

        try {
            JWT::decode($token, new Key($config['secret'], 'HS256'));
        } catch (\Exception $e) {
        }

        return self::generate($type, $payload->user_id);
    }
}
