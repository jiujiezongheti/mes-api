<?php

use Phinx\Migration\AbstractMigration;

class CreateBomTables extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('bom', ['id' => false, 'primary_key' => 'id', 'comment' => 'BOM表']);
        $table->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true, 'comment' => '主键ID'])
            ->addColumn('code', 'string', ['limit' => 50, 'comment' => 'BOM编号'])
            ->addColumn('name', 'string', ['limit' => 100, 'comment' => 'BOM名称'])
            ->addColumn('material_id', 'biginteger', ['signed' => false, 'comment' => '成品/半成品物料ID'])
            ->addColumn('quantity', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => 1, 'comment' => '产出数量'])
            ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态：0禁用 1启用'])
            ->addColumn('sort', 'integer', ['default' => 0, 'comment' => '排序'])
            ->addColumn('remark', 'text', ['null' => true, 'comment' => '备注'])
            ->addColumn('created_at', 'datetime', ['comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'comment' => '更新时间'])
            ->addColumn('deleted_at', 'datetime', ['null' => true, 'comment' => '删除时间'])
            ->addColumn('created_by', 'integer', ['null' => true, 'comment' => '创建人ID'])
            ->addIndex(['code'], ['unique' => true])
            ->addIndex(['material_id'])
            ->create();

        $detail = $this->table('bom_material', ['id' => false, 'primary_key' => 'id', 'comment' => 'BOM物料明细表']);
        $detail->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true, 'comment' => '主键ID'])
            ->addColumn('bom_id', 'biginteger', ['signed' => false, 'comment' => 'BOM ID'])
            ->addColumn('material_id', 'biginteger', ['signed' => false, 'comment' => '原材料ID'])
            ->addColumn('quantity', 'decimal', ['precision' => 10, 'scale' => 2, 'comment' => '用量'])
            ->addColumn('sort', 'integer', ['default' => 0, 'comment' => '排序'])
            ->addColumn('remark', 'text', ['null' => true, 'comment' => '备注'])
            ->addIndex(['bom_id'])
            ->addIndex(['material_id'])
            ->create();
    }
}
