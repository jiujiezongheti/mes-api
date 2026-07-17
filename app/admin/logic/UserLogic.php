<?php

namespace app\admin\logic;

use app\common\model\User;
use app\common\model\Role;
use app\common\model\RoleUser;
use app\common\exceptions\BusinessException;
use app\common\ResponseCode;
use app\common\utils\ExcelUtil;

class UserLogic
{
    public static function getList(array $params): array
    {
        $query = User::query();

        if (!empty($params['username'])) {
            $query->where('username', 'like', "%{$params['username']}%");
        }
        if (!empty($params['nickname'])) {
            $query->where('nickname', 'like', "%{$params['nickname']}%");
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', (int)$params['status']);
        }

        $page = (int)($params['page'] ?? 1);
        $pageSize = (int)($params['pageSize'] ?? 20);

        $total = $query->count();
        $list = $query->orderBy('id', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        $roleIds = RoleUser::whereIn('user_id', $list->pluck('id'))->pluck('role_id', 'user_id');
        $roles = Role::whereIn('id', $roleIds->unique()->values())->get()->keyBy('id');

        $result = $list->map(function ($user) use ($roleIds, $roles) {
            $userArr = $user->toArray();
            $userArr['role_ids'] = [];
            if ($roleIds->has($user->id)) {
                $rid = $roleIds->get($user->id);
                $userArr['role_ids'] = [$rid];
                $userArr['role_name'] = $roles->get($rid)?->name ?? '';
            }
            return $userArr;
        });

        return [
            'list' => $result,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
        ];
    }

    public static function create(array $data): void
    {
        if (User::where('username', $data['username'])->exists()) {
            throw new BusinessException('用户名已存在', ResponseCode::ERROR_PARAM->value);
        }

        $user = new User();
        $user->username = $data['username'];
        $user->password = password_hash($data['password'], PASSWORD_BCRYPT);
        $user->nickname = $data['nickname'] ?? $data['username'];
        $user->phone = $data['phone'] ?? null;
        $user->email = $data['email'] ?? null;
        $user->status = $data['status'] ?? 1;
        $user->sort = $data['sort'] ?? 0;
        $user->remark = $data['remark'] ?? null;
        $user->created_by = $data['created_by'] ?? null;
        $user->save();

        if (!empty($data['role_ids'])) {
            foreach ((array)$data['role_ids'] as $roleId) {
                RoleUser::insert(['role_id' => $roleId, 'user_id' => $user->id]);
            }
        }
    }

    public static function update(int $id, array $data): void
    {
        $user = User::find($id);
        if (!$user) {
            throw new BusinessException('用户不存在', ResponseCode::ERROR_PARAM->value);
        }

        if (isset($data['username']) && $data['username'] !== $user->username) {
            if (User::where('username', $data['username'])->exists()) {
                throw new BusinessException('用户名已存在', ResponseCode::ERROR_PARAM->value);
            }
            $user->username = $data['username'];
        }

        if (!empty($data['password'])) {
            $user->password = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        if (isset($data['nickname'])) $user->nickname = $data['nickname'];
        if (isset($data['phone'])) $user->phone = $data['phone'];
        if (isset($data['email'])) $user->email = $data['email'];
        if (isset($data['status'])) $user->status = (int)$data['status'];
        if (isset($data['sort'])) $user->sort = (int)$data['sort'];
        if (isset($data['remark'])) $user->remark = $data['remark'];
        $user->save();

        if (isset($data['role_ids'])) {
            RoleUser::where('user_id', $id)->delete();
            foreach ((array)$data['role_ids'] as $roleId) {
                RoleUser::insert(['role_id' => $roleId, 'user_id' => $id]);
            }
        }
    }

    public static function delete(int $id): void
    {
        $user = User::find($id);
        if (!$user) {
            throw new BusinessException('用户不存在', ResponseCode::ERROR_PARAM->value);
        }
        RoleUser::where('user_id', $id)->delete();
        $user->delete();
    }

    public static function export(?array $ids = null): string
    {
        $query = User::query();
        if ($ids !== null) {
            $query->whereIn('id', $ids);
        }
        $users = $query->orderBy('id')->get();

        $roleIds = RoleUser::whereIn('user_id', $users->pluck('id'))->pluck('role_id', 'user_id');
        $roles = Role::whereIn('id', $roleIds->unique()->values())->get()->keyBy('id');

        $headers = ['ID', '用户名', '昵称', '手机号', '邮箱', '角色', '状态'];
        $rows = $users->map(function ($user) use ($roleIds, $roles) {
            $rid = $roleIds->get($user->id);
            $roleName = $rid ? ($roles->get($rid)?->name ?? '') : '';
            return [
                $user->id,
                $user->username,
                $user->nickname ?? '',
                $user->phone ?? '',
                $user->email ?? '',
                $roleName,
                $user->status ? '启用' : '禁用',
            ];
        })->toArray();

        return ExcelUtil::export('用户', $headers, $rows);
    }

    public static function import(string $filePath): array
    {
        $data = ExcelUtil::import($filePath);
        $imported = 0;
        $errors = [];

        foreach ($data as $i => $row) {
            $line = $i + 2;
            $username = $row['用户名'] ?? '';
            if (empty($username)) {
                $errors[] = "第{$line}行：用户名为空";
                continue;
            }
            if (User::where('username', $username)->exists()) {
                $errors[] = "第{$line}行：用户名 '{$username}' 已存在";
                continue;
            }

            $user = new User();
            $user->username = $username;
            $user->password = password_hash('123456', PASSWORD_BCRYPT);
            $user->nickname = $row['昵称'] ?? $username;
            $user->phone = $row['手机号'] ?? null;
            $user->email = $row['邮箱'] ?? null;
            $user->status = ($row['状态'] ?? '启用') === '启用' ? 1 : 0;
            $user->save();
            $imported++;
        }

        return ['imported' => $imported, 'errors' => $errors];
    }
}
