<?php

namespace app\mobile\logic;

use app\common\model\User;
use app\common\exceptions\BusinessException;
use app\common\ResponseCode;
use app\common\utils\JwtUtil;

class AuthLogic
{
    public static function login(string $username, string $password): array
    {
        $user = User::where('username', $username)->first();

        if (!$user || !password_verify($password, $user->password)) {
            throw new BusinessException('账号或密码错误', ResponseCode::ERROR_AUTH->value);
        }

        if (!$user->status) {
            throw new BusinessException('账号已被禁用', ResponseCode::ERROR_FORBIDDEN->value);
        }

        $result = JwtUtil::generate('mobile', $user->id);
        $result['userInfo'] = $user->toArray();

        return $result;
    }

    public static function getUserInfo(int $userId): array
    {
        $user = User::find($userId);
        if (!$user) {
            throw new BusinessException('用户不存在', ResponseCode::ERROR_AUTH->value);
        }
        return $user->toArray();
    }

    public static function refresh(string $token): array
    {
        $result = JwtUtil::refresh($token, 'mobile');
        $user = User::find($result['user_id']);
        $result['userInfo'] = $user ? $user->toArray() : null;

        return $result;
    }
}
