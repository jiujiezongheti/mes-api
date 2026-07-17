<?php

use Phinx\Migration\AbstractMigration;

class CreateRoleUserTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('role_user', ['id' => false, 'primary_key' => 'id', 'comment' => '用户角色关联表']);
        $table->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true, 'comment' => '主键ID'])
            ->addColumn('role_id', 'biginteger', ['comment' => '角色ID'])
            ->addColumn('user_id', 'biginteger', ['comment' => '用户ID'])
            ->addIndex(['role_id', 'user_id'], ['unique' => true])
            ->create();
    }
}
