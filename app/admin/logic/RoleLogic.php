<?php

namespace app\admin\logic;

use app\common\model\Role;
use app\common\model\RolePermission;
use app\common\model\RoleUser;
use app\common\exceptions\BusinessException;
use app\common\ResponseCode;
use app\common\utils\ExcelUtil;

class RoleLogic
{
    public static function getList(int $page = 1, int $pageSize = 20, array $params = []): array
    {
        $query = Role::orderBy('sort')->orderBy('id');

        if (!empty($params['name'])) {
            $query->where('name', 'like', "%{$params['name']}%");
        }
        if (!empty($params['code'])) {
            $query->where('code', 'like', "%{$params['code']}%");
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', (int)$params['status']);
        }

        $total = $query->count();
        $list = $query->forPage($page, $pageSize)->get()->toArray();
        return ['list' => $list, 'total' => $total];
    }

    public static function getAll(): array
    {
        return Role::where('status', true)->orderBy('sort')->orderBy('id')->get()->toArray();
    }

    public static function create(array $data): void
    {
        if (Role::where('code', $data['code'])->exists()) {
            throw new BusinessException('角色标识已存在', ResponseCode::ERROR_PARAM->value);
        }

        $role = new Role();
        $role->name = $data['name'];
        $role->code = $data['code'];
        $role->status = $data['status'] ?? 1;
        $role->sort = $data['sort'] ?? 0;
        $role->remark = $data['remark'] ?? null;
        $role->created_by = $data['created_by'] ?? null;
        $role->save();
    }

    public static function update(int $id, array $data): void
    {
        $role = Role::find($id);
        if (!$role) {
            throw new BusinessException('角色不存在', ResponseCode::ERROR_PARAM->value);
        }

        if (isset($data['code']) && $data['code'] !== $role->code) {
            if (Role::where('code', $data['code'])->exists()) {
                throw new BusinessException('角色标识已存在', ResponseCode::ERROR_PARAM->value);
            }
            $role->code = $data['code'];
        }

        if (isset($data['name'])) $role->name = $data['name'];
        if (isset($data['status'])) $role->status = (int)$data['status'];
        if (isset($data['sort'])) $role->sort = (int)$data['sort'];
        if (isset($data['remark'])) $role->remark = $data['remark'];
        $role->save();
    }

    public static function delete(int $id): void
    {
        $role = Role::find($id);
        if (!$role) {
            throw new BusinessException('角色不存在', ResponseCode::ERROR_PARAM->value);
        }
        RolePermission::where('role_id', $id)->delete();
        RoleUser::where('role_id', $id)->delete();
        $role->delete();
    }

    public static function getPermissionIds(int $id): array
    {
        return RolePermission::where('role_id', $id)->pluck('permission_id')->toArray();
    }

    public static function bindPermissions(int $id, array $permissionIds): void
    {
        $role = Role::find($id);
        if (!$role) {
            throw new BusinessException('角色不存在', ResponseCode::ERROR_PARAM->value);
        }

        RolePermission::where('role_id', $id)->delete();
        foreach ($permissionIds as $pid) {
            RolePermission::insert(['role_id' => $id, 'permission_id' => $pid]);
        }
    }

    public static function import(string $filePath): array
    {
        $data = ExcelUtil::import($filePath);
        $imported = 0;
        $errors = [];

        foreach ($data as $i => $row) {
            $line = $i + 2;
            $name = $row['角色名称'] ?? '';
            $code = $row['角色标识'] ?? '';
            if (empty($name) || empty($code)) {
                $errors[] = "第{$line}行：角色名称和角色标识不能为空";
                continue;
            }
            if (Role::where('code', $code)->exists()) {
                $errors[] = "第{$line}行：角色标识 '{$code}' 已存在";
                continue;
            }

            $role = new Role();
            $role->name = $name;
            $role->code = $code;
            $role->status = ($row['状态'] ?? '启用') === '启用' ? 1 : 0;
            $role->remark = $row['备注'] ?? null;
            $role->save();
            $imported++;
        }

        return ['imported' => $imported, 'errors' => $errors];
    }

    public static function export(?array $ids = null): string
    {
        $query = Role::query();
        if ($ids !== null) {
            $query->whereIn('id', $ids);
        }
        $roles = $query->orderBy('id')->get();

        $headers = ['ID', '角色名称', '角色标识', '状态', '备注'];
        $rows = $roles->map(function ($role) {
            return [
                $role->id,
                $role->name,
                $role->code,
                $role->status ? '启用' : '禁用',
                $role->remark ?? '',
            ];
        })->toArray();

        return ExcelUtil::export('角色', $headers, $rows);
    }
}
