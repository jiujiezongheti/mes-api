<?php

use Phinx\Migration\AbstractMigration;

class CreatePermissionTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('permission', ['id' => false, 'primary_key' => 'id', 'comment' => '权限表']);
        $table->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true, 'comment' => '主键ID'])
            ->addColumn('parent_id', 'biginteger', ['null' => true, 'comment' => '父级ID'])
            ->addColumn('name', 'string', ['limit' => 50, 'comment' => '权限名称'])
            ->addColumn('code', 'string', ['limit' => 100, 'null' => true, 'comment' => '权限标识'])
            ->addColumn('type', 'string', ['limit' => 20, 'comment' => '类型：dir目录 menu菜单 btn按钮'])
            ->addColumn('sort', 'integer', ['default' => 0, 'comment' => '排序'])
            ->addColumn('created_at', 'datetime', ['comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'comment' => '更新时间'])
            ->addIndex(['code'], ['unique' => true])
            ->create();
    }
}
