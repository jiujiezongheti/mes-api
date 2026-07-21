<?php

use Phinx\Seed\AbstractSeed;

class MaterialCategoryPermissionSeeder extends AbstractSeed
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $permissions = [
            ['id' => 57, 'parent_id' => 50, 'code' => 'admin:material:category',           'name' => '物料分类',    'type' => 'menu'],
            ['id' => 58, 'parent_id' => 57, 'code' => 'admin:material:category:create',     'name' => '新增分类',    'type' => 'btn'],
            ['id' => 59, 'parent_id' => 57, 'code' => 'admin:material:category:edit',       'name' => '编辑分类',    'type' => 'btn'],
            ['id' => 60, 'parent_id' => 57, 'code' => 'admin:material:category:delete',     'name' => '删除分类',    'type' => 'btn'],
            ['id' => 61, 'parent_id' => 57, 'code' => 'admin:material:category:export',     'name' => '导出分类',    'type' => 'btn'],
            ['id' => 62, 'parent_id' => 57, 'code' => 'admin:material:category:import',     'name' => '导入分类',    'type' => 'btn'],
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
            ['role_id' => 1, 'permission_id' => 57],
            ['role_id' => 1, 'permission_id' => 58],
            ['role_id' => 1, 'permission_id' => 59],
            ['role_id' => 1, 'permission_id' => 60],
            ['role_id' => 1, 'permission_id' => 61],
            ['role_id' => 1, 'permission_id' => 62],
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
