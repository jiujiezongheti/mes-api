<?php

use Phinx\Seed\AbstractSeed;

class UnitPermissionSeeder extends AbstractSeed
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $permissions = [
            ['id' => 63, 'parent_id' => 50, 'code' => 'admin:unit:list',       'name' => '计量单位',    'type' => 'menu'],
            ['id' => 64, 'parent_id' => 63, 'code' => 'admin:unit:create',     'name' => '新增单位',    'type' => 'btn'],
            ['id' => 65, 'parent_id' => 63, 'code' => 'admin:unit:edit',       'name' => '编辑单位',    'type' => 'btn'],
            ['id' => 66, 'parent_id' => 63, 'code' => 'admin:unit:delete',     'name' => '删除单位',    'type' => 'btn'],
            ['id' => 67, 'parent_id' => 63, 'code' => 'admin:unit:export',     'name' => '导出单位',    'type' => 'btn'],
            ['id' => 68, 'parent_id' => 63, 'code' => 'admin:unit:import',     'name' => '导入单位',    'type' => 'btn'],
        ];

        foreach ($permissions as $perm) {
            try {
                $this->table('permission')->insert([
                    $perm + ['sort' => 0, 'created_at' => $now],
                ])->save();
            } catch (\Exception $e) {
                // permission already exists, skip
            }
        }

        $rolePerms = [
            ['role_id' => 1, 'permission_id' => 63],
            ['role_id' => 1, 'permission_id' => 64],
            ['role_id' => 1, 'permission_id' => 65],
            ['role_id' => 1, 'permission_id' => 66],
            ['role_id' => 1, 'permission_id' => 67],
            ['role_id' => 1, 'permission_id' => 68],
        ];

        foreach ($rolePerms as $rp) {
            try {
                $this->table('role_permission')->insert([$rp])->save();
            } catch (\Exception $e) {
                // already bound, skip
            }
        }
    }
}
