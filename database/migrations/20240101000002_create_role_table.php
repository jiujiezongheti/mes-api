<?php

use Phinx\Migration\AbstractMigration;

class CreateRoleTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('role', ['id' => false, 'primary_key' => 'id', 'comment' => '角色表']);
        $table->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true, 'comment' => '主键ID'])
            ->addColumn('name', 'string', ['limit' => 50, 'comment' => '角色名称'])
            ->addColumn('code', 'string', ['limit' => 50, 'comment' => '角色标识'])
            ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态：0禁用 1启用'])
            ->addColumn('sort', 'integer', ['default' => 0, 'comment' => '排序'])
            ->addColumn('remark', 'text', ['null' => true, 'comment' => '备注'])
            ->addColumn('created_at', 'datetime', ['comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'comment' => '更新时间'])
            ->addColumn('deleted_at', 'datetime', ['null' => true, 'comment' => '删除时间'])
            ->addColumn('created_by', 'integer', ['null' => true, 'comment' => '创建人ID'])
            ->addIndex(['code'], ['unique' => true])
            ->create();
    }
}
