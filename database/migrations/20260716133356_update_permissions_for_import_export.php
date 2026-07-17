<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UpdatePermissionsForImportExport extends AbstractMigration
{
    public function change(): void
    {
        $now = date('Y-m-d H:i:s');

        $this->execute("DELETE FROM role_permission WHERE permission_id IN (10, 11)");
        $this->execute("DELETE FROM permission WHERE id IN (10, 11)");

        $this->table('permission')->insert([
            ['id' => 10, 'parent_id' => 2,  'code' => 'admin:user:import', 'name' => '导入用户', 'type' => 'btn', 'sort' => 0, 'created_at' => $now],
            ['id' => 11, 'parent_id' => 2,  'code' => 'admin:user:export', 'name' => '导出用户', 'type' => 'btn', 'sort' => 0, 'created_at' => $now],
            ['id' => 12, 'parent_id' => 6,  'code' => 'admin:role:export', 'name' => '导出角色', 'type' => 'btn', 'sort' => 0, 'created_at' => $now],
        ])->save();

        $adminRolePerms = [10, 11, 12];
        foreach ($adminRolePerms as $pid) {
            $this->table('role_permission')->insert(['role_id' => 1, 'permission_id' => $pid])->save();
        }
    }
}
