<?php

use Phinx\Migration\AbstractMigration;

class CreateUserTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('user', ['id' => false, 'primary_key' => 'id', 'comment' => '用户表']);
        $table->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true, 'comment' => '主键ID'])
            ->addColumn('username', 'string', ['limit' => 50, 'comment' => '用户名'])
            ->addColumn('password', 'string', ['limit' => 255, 'comment' => '密码（bcrypt 哈希）'])
            ->addColumn('nickname', 'string', ['limit' => 50, 'comment' => '昵称'])
            ->addColumn('avatar', 'string', ['limit' => 255, 'null' => true, 'comment' => '头像地址'])
            ->addColumn('phone', 'string', ['limit' => 20, 'null' => true, 'comment' => '手机号'])
            ->addColumn('email', 'string', ['limit' => 100, 'null' => true, 'comment' => '邮箱'])
            ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态：0禁用 1启用'])
            ->addColumn('sort', 'integer', ['default' => 0, 'comment' => '排序'])
            ->addColumn('remark', 'text', ['null' => true, 'comment' => '备注'])
            ->addColumn('created_at', 'datetime', ['comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'comment' => '更新时间'])
            ->addColumn('deleted_at', 'datetime', ['null' => true, 'comment' => '删除时间'])
            ->addColumn('created_by', 'integer', ['null' => true, 'comment' => '创建人ID'])
            ->addIndex(['username'], ['unique' => true])
            ->create();
    }
}
