<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddRoleImportPermission extends AbstractMigration
{
    public function change(): void
    {
        $now = date('Y-m-d H:i:s');

        $this->table('permission')->insert([
            ['id' => 13, 'parent_id' => 6, 'code' => 'admin:role:import', 'name' => '导入角色', 'type' => 'btn', 'sort' => 0, 'created_at' => $now],
        ])->save();

        $this->table('role_permission')->insert(['role_id' => 1, 'permission_id' => 13])->save();
    }
}
