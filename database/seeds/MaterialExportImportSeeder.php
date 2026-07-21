<?php

use Phinx\Seed\AbstractSeed;

class MaterialExportImportSeeder extends AbstractSeed
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $this->table('permission')->insert([
            ['id' => 55, 'parent_id' => 51, 'code' => 'admin:material:export', 'name' => '导出物料', 'type' => 'btn', 'sort' => 0, 'created_at' => $now],
            ['id' => 56, 'parent_id' => 51, 'code' => 'admin:material:import', 'name' => '导入物料', 'type' => 'btn', 'sort' => 0, 'created_at' => $now],
        ])->save();

        $this->table('role_permission')->insert([
            ['role_id' => 1, 'permission_id' => 55],
            ['role_id' => 1, 'permission_id' => 56],
        ])->save();
    }
}
