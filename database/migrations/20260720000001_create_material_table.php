<?php

use Phinx\Migration\AbstractMigration;

class CreateMaterialTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('material', ['id' => false, 'primary_key' => 'id', 'comment' => '物料表']);
        $table->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true, 'comment' => '主键ID'])
            ->addColumn('code', 'string', ['limit' => 50, 'comment' => '物料编码'])
            ->addColumn('name', 'string', ['limit' => 100, 'comment' => '物料名称'])
            ->addColumn('spec', 'string', ['limit' => 100, 'null' => true, 'comment' => '规格型号'])
            ->addColumn('unit', 'string', ['limit' => 20, 'null' => true, 'comment' => '计量单位'])
            ->addColumn('type', 'tinyinteger', ['default' => 1, 'comment' => '类型：1原材料 2半成品 3成品 4辅料'])
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
