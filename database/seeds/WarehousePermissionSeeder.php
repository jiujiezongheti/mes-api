<?php

use Phinx\Seed\AbstractSeed;

class WarehousePermissionSeeder extends AbstractSeed
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $permissions = [
            // ===== 仓库库存 =====
            ['id' => 80, 'parent_id' => null, 'code' => 'warehouse',             'name' => '仓库库存',  'type' => 'dir'],
            ['id' => 81, 'parent_id' => 80,   'code' => 'admin:warehouse:list',  'name' => '仓库管理',  'type' => 'menu'],
            ['id' => 82, 'parent_id' => 81,   'code' => 'admin:warehouse:create','name' => '新增仓库',  'type' => 'btn'],
            ['id' => 83, 'parent_id' => 81,   'code' => 'admin:warehouse:edit',  'name' => '编辑仓库',  'type' => 'btn'],
            ['id' => 84, 'parent_id' => 81,   'code' => 'admin:warehouse:delete','name' => '删除仓库',  'type' => 'btn'],
            ['id' => 85, 'parent_id' => 80,   'code' => 'admin:stock:list',      'name' => '库存管理',  'type' => 'menu'],
            ['id' => 86, 'parent_id' => 85,   'code' => 'admin:stock:in',        'name' => '入库',     'type' => 'btn'],
            ['id' => 87, 'parent_id' => 85,   'code' => 'admin:stock:out',       'name' => '出库',     'type' => 'btn'],
            ['id' => 88, 'parent_id' => 85,   'code' => 'admin:stock:check',     'name' => '库存盘点',  'type' => 'btn'],
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

        $rolePerms = [];
        foreach ($permissions as $perm) {
            $rolePerms[] = ['role_id' => 1, 'permission_id' => $perm['id']];
        }

        foreach ($rolePerms as $rp) {
            try {
                $this->table('role_permission')->insert([$rp])->save();
            } catch (\Exception $e) {
                // already bound, skip
            }
        }
    }
}
