<?php

namespace app\admin\logic;

use app\common\model\User;
use app\common\model\RoleUser;
use app\common\model\RolePermission;
use app\common\model\Permission;
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

        $result = JwtUtil::generate('admin', $user->id);
        $result['userInfo'] = $user->toArray();
        $result['permissions'] = self::getUserPermissions($user->id);

        return $result;
    }

    public static function getUserInfo(int $userId): array
    {
        $user = User::find($userId);
        if (!$user) {
            throw new BusinessException('用户不存在', ResponseCode::ERROR_AUTH->value);
        }

        $info = $user->toArray();
        $info['permissions'] = self::getUserPermissions($userId);

        return $info;
    }

    public static function getUserPermissions(int $userId): array
    {
        $roleIds = RoleUser::where('user_id', $userId)->pluck('role_id')->toArray();
        if (empty($roleIds)) {
            return [];
        }

        $permissionIds = RolePermission::whereIn('role_id', $roleIds)->pluck('permission_id')->toArray();
        if (empty($permissionIds)) {
            return [];
        }

        return Permission::whereIn('id', $permissionIds)
            ->whereNotNull('code')
            ->pluck('code')
            ->toArray();
    }

    public static function updatePassword(int $userId, string $oldPassword, string $newPassword): void
    {
        $user = User::find($userId);
        if (!$user) {
            throw new BusinessException('用户不存在', ResponseCode::ERROR_AUTH->value);
        }
        if (!password_verify($oldPassword, $user->password)) {
            throw new BusinessException('原密码错误', ResponseCode::ERROR_AUTH->value);
        }
        $user->password = password_hash($newPassword, PASSWORD_BCRYPT);
        $user->save();
    }

    public static function refresh(string $token): array
    {
        $result = JwtUtil::refresh($token, 'admin');
        $user = User::find($result['user_id']);
        $result['userInfo'] = $user ? $user->toArray() : null;
        $result['permissions'] = $user ? self::getUserPermissions($user->id) : [];

        return $result;
    }
}
