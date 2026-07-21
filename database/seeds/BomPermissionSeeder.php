<?php

use Phinx\Seed\AbstractSeed;

class BomPermissionSeeder extends AbstractSeed
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $permissions = [
            ['id' => 70, 'parent_id' => 20, 'code' => 'admin:bom:list',       'name' => 'BOM管理',    'type' => 'menu'],
            ['id' => 71, 'parent_id' => 70, 'code' => 'admin:bom:create',     'name' => '新增BOM',    'type' => 'btn'],
            ['id' => 72, 'parent_id' => 70, 'code' => 'admin:bom:edit',       'name' => '编辑BOM',    'type' => 'btn'],
            ['id' => 73, 'parent_id' => 70, 'code' => 'admin:bom:delete',     'name' => '删除BOM',    'type' => 'btn'],
        ];

        foreach ($permissions as $perm) {
            try {
                $this->table('permission')->insert([
                    $perm + ['sort' => 0, 'created_at' => $now],
                ])->save();
            } catch (\Exception $e) {
            }
        }

        $rolePerms = [
            ['role_id' => 1, 'permission_id' => 70],
            ['role_id' => 1, 'permission_id' => 71],
            ['role_id' => 1, 'permission_id' => 72],
            ['role_id' => 1, 'permission_id' => 73],
        ];

        foreach ($rolePerms as $rp) {
            try {
                $this->table('role_permission')->insert([$rp])->save();
            } catch (\Exception $e) {
            }
        }
    }
}
