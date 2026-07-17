<?php

use Phinx\Seed\AbstractSeed;

class PermissionSeeder extends AbstractSeed
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $permissions = [
            // ===== 系统管理 =====
            ['id' => 1,  'parent_id' => null, 'code' => 'system',                    'name' => '系统管理',     'type' => 'dir'],
            ['id' => 2,  'parent_id' => 1,    'code' => 'admin:user:list',            'name' => '用户管理',     'type' => 'menu'],
            ['id' => 3,  'parent_id' => 2,    'code' => 'admin:user:create',          'name' => '新增用户',     'type' => 'btn'],
            ['id' => 4,  'parent_id' => 2,    'code' => 'admin:user:edit',            'name' => '编辑用户',     'type' => 'btn'],
            ['id' => 5,  'parent_id' => 2,    'code' => 'admin:user:delete',          'name' => '删除用户',     'type' => 'btn'],
            ['id' => 6,  'parent_id' => 1,    'code' => 'admin:role:list',            'name' => '角色管理',     'type' => 'menu'],
            ['id' => 7,  'parent_id' => 6,    'code' => 'admin:role:create',          'name' => '新增角色',     'type' => 'btn'],
            ['id' => 8,  'parent_id' => 6,    'code' => 'admin:role:edit',            'name' => '编辑角色',     'type' => 'btn'],
            ['id' => 9,  'parent_id' => 6,    'code' => 'admin:role:delete',          'name' => '删除角色',     'type' => 'btn'],
            ['id' => 10, 'parent_id' => 2,    'code' => 'admin:user:import',          'name' => '导入用户',     'type' => 'btn'],
            ['id' => 11, 'parent_id' => 2,    'code' => 'admin:user:export',          'name' => '导出用户',     'type' => 'btn'],
            ['id' => 12, 'parent_id' => 6,    'code' => 'admin:role:export',          'name' => '导出角色',     'type' => 'btn'],
            ['id' => 13, 'parent_id' => 6,    'code' => 'admin:role:import',          'name' => '导入角色',     'type' => 'btn'],

            // ===== 生产管理 =====
            ['id' => 20, 'parent_id' => null, 'code' => 'production',                 'name' => '生产管理',     'type' => 'dir'],
            ['id' => 21, 'parent_id' => 20,   'code' => 'admin:order:list',           'name' => '工单管理',     'type' => 'menu'],
            ['id' => 22, 'parent_id' => 21,   'code' => 'admin:order:create',         'name' => '新增工单',     'type' => 'btn'],
            ['id' => 23, 'parent_id' => 21,   'code' => 'admin:order:edit',           'name' => '编辑工单',     'type' => 'btn'],
            ['id' => 24, 'parent_id' => 21,   'code' => 'admin:order:delete',         'name' => '删除工单',     'type' => 'btn'],
            ['id' => 25, 'parent_id' => 21,   'code' => 'admin:order:export',         'name' => '导出工单',     'type' => 'btn'],
            ['id' => 26, 'parent_id' => 20,   'code' => 'admin:report:list',          'name' => '报工管理',     'type' => 'menu'],
            ['id' => 27, 'parent_id' => 26,   'code' => 'admin:report:create',        'name' => '新增报工',     'type' => 'btn'],
            ['id' => 28, 'parent_id' => 26,   'code' => 'admin:report:edit',          'name' => '编辑报工',     'type' => 'btn'],

            // ===== 质量管理 =====
            ['id' => 30, 'parent_id' => null, 'code' => 'quality',                    'name' => '质量管理',     'type' => 'dir'],
            ['id' => 31, 'parent_id' => 30,   'code' => 'admin:quality:list',         'name' => '质检记录',     'type' => 'menu'],
            ['id' => 32, 'parent_id' => 31,   'code' => 'admin:quality:create',       'name' => '新增质检',     'type' => 'btn'],
            ['id' => 33, 'parent_id' => 31,   'code' => 'admin:quality:edit',         'name' => '编辑质检',     'type' => 'btn'],
            ['id' => 34, 'parent_id' => 31,   'code' => 'admin:quality:delete',       'name' => '删除质检',     'type' => 'btn'],

            // ===== 设备管理 =====
            ['id' => 40, 'parent_id' => null, 'code' => 'equipment',                  'name' => '设备管理',     'type' => 'dir'],
            ['id' => 41, 'parent_id' => 40,   'code' => 'admin:device:list',          'name' => '设备台账',     'type' => 'menu'],
            ['id' => 42, 'parent_id' => 41,   'code' => 'admin:device:create',        'name' => '新增设备',     'type' => 'btn'],
            ['id' => 43, 'parent_id' => 41,   'code' => 'admin:device:edit',          'name' => '编辑设备',     'type' => 'btn'],
            ['id' => 44, 'parent_id' => 41,   'code' => 'admin:device:delete',        'name' => '删除设备',     'type' => 'btn'],

            // ===== 物料管理 =====
            ['id' => 50, 'parent_id' => null, 'code' => 'material',                   'name' => '物料管理',     'type' => 'dir'],
            ['id' => 51, 'parent_id' => 50,   'code' => 'admin:material:list',        'name' => '物料档案',     'type' => 'menu'],
            ['id' => 52, 'parent_id' => 51,   'code' => 'admin:material:create',      'name' => '新增物料',     'type' => 'btn'],
            ['id' => 53, 'parent_id' => 51,   'code' => 'admin:material:edit',        'name' => '编辑物料',     'type' => 'btn'],
            ['id' => 54, 'parent_id' => 51,   'code' => 'admin:material:delete',      'name' => '删除物料',     'type' => 'btn'],
        ];

        $permissionTable = $this->table('permission');
        foreach ($permissions as $perm) {
            $perm['sort'] = 0;
            $perm['created_at'] = $now;
            $permissionTable->insert($perm)->save();
        }

        $roleTable = $this->table('role');
        $roleTable->insert([
            ['name' => '超级管理员', 'code' => 'admin',     'status' => 1, 'sort' => 0, 'remark' => '拥有所有权限', 'created_at' => $now],
            ['name' => '生产操作员', 'code' => 'operator',  'status' => 1, 'sort' => 1, 'remark' => '生产模块操作', 'created_at' => $now],
            ['name' => '质检员',     'code' => 'inspector', 'status' => 1, 'sort' => 2, 'remark' => '质量模块操作', 'created_at' => $now],
        ])->save();

        $adminPermissionIds = array_column($permissions, 'id');
        $rpTable = $this->table('role_permission');
        foreach ($adminPermissionIds as $pid) {
            $rpTable->insert(['role_id' => 1, 'permission_id' => $pid])->save();
        }

        $operatorPermIds = [20, 21, 22, 23, 24, 25, 26, 27, 28];
        foreach ($operatorPermIds as $pid) {
            $rpTable->insert(['role_id' => 2, 'permission_id' => $pid])->save();
        }

        $inspectorPermIds = [30, 31, 32, 33, 34];
        foreach ($inspectorPermIds as $pid) {
            $rpTable->insert(['role_id' => 3, 'permission_id' => $pid])->save();
        }

        $this->table('role_user')->insert([
            ['role_id' => 1, 'user_id' => 1],
        ])->save();
    }
}
