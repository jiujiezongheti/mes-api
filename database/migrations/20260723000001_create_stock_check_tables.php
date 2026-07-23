<?php

use Phinx\Migration\AbstractMigration;

final class CreateStockCheckTables extends AbstractMigration
{
    public function change(): void
    {
        // ===== 盘点任务 =====
        $this->table('stock_check_task', ['id' => false, 'primary_key' => 'id', 'comment' => '盘点任务表'])
            ->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true])
            ->addColumn('code', 'string', ['limit' => 50, 'comment' => '任务编号'])
            ->addColumn('warehouse_id', 'biginteger', ['signed' => false, 'comment' => '仓库ID'])
            ->addColumn('status', 'tinyinteger', ['default' => 0, 'comment' => '状态：0待盘点 1已完成 2已审核 3已驳回'])
            ->addColumn('remark', 'text', ['null' => true, 'comment' => '备注'])
            ->addColumn('created_at', 'datetime', ['comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'comment' => '更新时间'])
            ->addColumn('deleted_at', 'datetime', ['null' => true, 'comment' => '删除时间'])
            ->addColumn('created_by', 'integer', ['null' => true, 'comment' => '创建人ID'])
            ->addIndex(['code'], ['unique' => true])
            ->addForeignKey('warehouse_id', 'warehouse', 'id')
            ->create();

        // ===== 盘点记录（扫码结果） =====
        $this->table('stock_check_record', ['id' => false, 'primary_key' => 'id', 'comment' => '盘点记录表'])
            ->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true])
            ->addColumn('task_id', 'biginteger', ['signed' => false, 'comment' => '任务ID'])
            ->addColumn('material_id', 'biginteger', ['signed' => false, 'comment' => '物料ID'])
            ->addColumn('batch_no', 'string', ['limit' => 100, 'null' => true, 'default' => '', 'comment' => '批次号'])
            ->addColumn('actual_quantity', 'decimal', ['precision' => 12, 'scale' => 2, 'comment' => '实盘数量'])
            ->addColumn('remark', 'string', ['limit' => 255, 'null' => true, 'comment' => '备注'])
            ->addColumn('created_at', 'datetime', ['comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'comment' => '更新时间'])
            ->addColumn('deleted_at', 'datetime', ['null' => true, 'comment' => '删除时间'])
            ->addColumn('created_by', 'integer', ['null' => true, 'comment' => '盘点人ID'])
            ->addIndex(['task_id'])
            ->addIndex(['material_id'])
            ->addForeignKey('task_id', 'stock_check_task', 'id', ['delete' => 'CASCADE'])
            ->addForeignKey('material_id', 'material', 'id')
            ->create();
    }
}
