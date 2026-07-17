<?php

use Phinx\Migration\AbstractMigration;

class CreateRolePermissionTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('role_permission', ['id' => false, 'primary_key' => 'id', 'comment' => '角色权限关联表']);
        $table->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true, 'comment' => '主键ID'])
            ->addColumn('role_id', 'biginteger', ['comment' => '角色ID'])
            ->addColumn('permission_id', 'biginteger', ['comment' => '权限ID'])
            ->addIndex(['role_id', 'permission_id'], ['unique' => true])
            ->create();
    }
}
