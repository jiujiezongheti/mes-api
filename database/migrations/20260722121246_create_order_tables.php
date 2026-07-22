<?php

use Phinx\Migration\AbstractMigration;

class CreateOrderTables extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('production_order', ['id' => false, 'primary_key' => 'id', 'comment' => '工单表']);
        $table->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true, 'comment' => '主键ID'])
            ->addColumn('code', 'string', ['limit' => 50, 'comment' => '工单编号'])
            ->addColumn('bom_id', 'biginteger', ['signed' => false, 'null' => true, 'comment' => '关联BOM ID'])
            ->addColumn('material_id', 'biginteger', ['signed' => false, 'comment' => '生产物料ID'])
            ->addColumn('quantity', 'decimal', ['precision' => 10, 'scale' => 2, 'comment' => '计划数量'])
            ->addColumn('produced_quantity', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => 0, 'comment' => '已完成数量'])
            ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态：1待生产 2生产中 3已完成 4已关闭'])
            ->addColumn('priority', 'integer', ['default' => 1, 'comment' => '优先级：1普通 2紧急'])
            ->addColumn('plan_start_date', 'datetime', ['null' => true, 'comment' => '计划开始时间'])
            ->addColumn('plan_end_date', 'datetime', ['null' => true, 'comment' => '计划结束时间'])
            ->addColumn('actual_start_date', 'datetime', ['null' => true, 'comment' => '实际开始时间'])
            ->addColumn('actual_end_date', 'datetime', ['null' => true, 'comment' => '实际结束时间'])
            ->addColumn('sort', 'integer', ['default' => 0, 'comment' => '排序'])
            ->addColumn('remark', 'text', ['null' => true, 'comment' => '备注'])
            ->addColumn('created_at', 'datetime', ['comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'comment' => '更新时间'])
            ->addColumn('deleted_at', 'datetime', ['null' => true, 'comment' => '删除时间'])
            ->addColumn('created_by', 'integer', ['null' => true, 'comment' => '创建人ID'])
            ->addIndex(['code'], ['unique' => true])
            ->addIndex(['bom_id'])
            ->addIndex(['material_id'])
            ->addIndex(['status'])
            ->create();

        $detail = $this->table('production_order_material', ['id' => false, 'primary_key' => 'id', 'comment' => '工单物料明细表']);
        $detail->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true, 'comment' => '主键ID'])
            ->addColumn('order_id', 'biginteger', ['signed' => false, 'comment' => '工单ID'])
            ->addColumn('material_id', 'biginteger', ['signed' => false, 'comment' => '物料ID'])
            ->addColumn('required_quantity', 'decimal', ['precision' => 10, 'scale' => 2, 'comment' => '需求数量'])
            ->addColumn('issued_quantity', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => 0, 'comment' => '已领料数量'])
            ->addColumn('remark', 'text', ['null' => true, 'comment' => '备注'])
            ->addIndex(['order_id'])
            ->addIndex(['material_id'])
            ->create();
    }
}
