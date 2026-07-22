<?php

use Phinx\Migration\AbstractMigration;

final class CreateWarehouseInventoryTables extends AbstractMigration
{
    public function change(): void
    {
        // ===== 仓库 =====
        $this->table('warehouse', ['id' => false, 'primary_key' => 'id', 'comment' => '仓库表'])
            ->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true])
            ->addColumn('code', 'string', ['limit' => 50, 'comment' => '仓库编码'])
            ->addColumn('name', 'string', ['limit' => 100, 'comment' => '仓库名称'])
            ->addColumn('type', 'tinyinteger', ['default' => 1, 'comment' => '类型：1原材料 2半成品 3成品 4辅料 5不良品 6待检'])
            ->addColumn('address', 'string', ['limit' => 255, 'null' => true, 'comment' => '地址'])
            ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态'])
            ->addColumn('sort', 'integer', ['default' => 0])
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('created_at', 'datetime', ['comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addColumn('created_by', 'integer', ['null' => true])
            ->addIndex(['code'], ['unique' => true])
            ->create();

        // ===== 库存台账 =====
        $this->table('inventory', ['id' => false, 'primary_key' => 'id', 'comment' => '库存台账表'])
            ->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true])
            ->addColumn('warehouse_id', 'biginteger', ['signed' => false, 'comment' => '仓库ID'])
            ->addColumn('material_id', 'biginteger', ['signed' => false, 'comment' => '物料ID'])
            ->addColumn('quantity', 'decimal', ['precision' => 12, 'scale' => 2, 'default' => 0, 'comment' => '当前库存'])
            ->addColumn('locked_quantity', 'decimal', ['precision' => 12, 'scale' => 2, 'default' => 0, 'comment' => '锁定数量'])
            ->addIndex(['warehouse_id', 'material_id'], ['unique' => true])
            ->addForeignKey('warehouse_id', 'warehouse', 'id')
            ->addForeignKey('material_id', 'material', 'id')
            ->create();

        // ===== 库存流水 =====
        $this->table('stock_record', ['id' => false, 'primary_key' => 'id', 'comment' => '库存流水表'])
            ->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true])
            ->addColumn('warehouse_id', 'biginteger', ['signed' => false, 'comment' => '仓库ID'])
            ->addColumn('material_id', 'biginteger', ['signed' => false, 'comment' => '物料ID'])
            ->addColumn('type', 'tinyinteger', ['comment' => '类型：1入库 2出库 3盘盈 4盘亏'])
            ->addColumn('quantity', 'decimal', ['precision' => 12, 'scale' => 2, 'comment' => '变动数量(入库正/出库负)'])
            ->addColumn('before_quantity', 'decimal', ['precision' => 12, 'scale' => 2, 'comment' => '变动前库存'])
            ->addColumn('after_quantity', 'decimal', ['precision' => 12, 'scale' => 2, 'comment' => '变动后库存'])
            ->addColumn('source_type', 'string', ['limit' => 50, 'null' => true, 'comment' => '来源类型(manual/order_out/order_in/check/transfer)'])
            ->addColumn('source_id', 'biginteger', ['signed' => false, 'null' => true, 'comment' => '来源单据ID'])
            ->addColumn('remark', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('created_by', 'integer', ['null' => true])
            ->addColumn('created_at', 'datetime', ['comment' => '创建时间'])
            ->addIndex(['warehouse_id'])
            ->addIndex(['material_id'])
            ->addIndex(['source_type', 'source_id'])
            ->addForeignKey('warehouse_id', 'warehouse', 'id')
            ->addForeignKey('material_id', 'material', 'id')
            ->create();

        // ===== 盘点单 =====
        $this->table('inventory_check', ['id' => false, 'primary_key' => 'id', 'comment' => '盘点单表'])
            ->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true])
            ->addColumn('code', 'string', ['limit' => 50, 'comment' => '盘点单号'])
            ->addColumn('warehouse_id', 'biginteger', ['signed' => false, 'comment' => '仓库ID'])
            ->addColumn('status', 'tinyinteger', ['default' => 1, 'comment' => '状态：1进行中 2已完成'])
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('created_by', 'integer', ['null' => true])
            ->addColumn('created_at', 'datetime', ['comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex(['code'], ['unique' => true])
            ->addForeignKey('warehouse_id', 'warehouse', 'id')
            ->create();

        // ===== 盘点明细 =====
        $this->table('inventory_check_item', ['id' => false, 'primary_key' => 'id', 'comment' => '盘点明细表'])
            ->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true])
            ->addColumn('check_id', 'biginteger', ['signed' => false, 'comment' => '盘点单ID'])
            ->addColumn('material_id', 'biginteger', ['signed' => false, 'comment' => '物料ID'])
            ->addColumn('book_quantity', 'decimal', ['precision' => 12, 'scale' => 2, 'comment' => '账面数量'])
            ->addColumn('actual_quantity', 'decimal', ['precision' => 12, 'scale' => 2, 'comment' => '实盘数量'])
            ->addColumn('remark', 'string', ['limit' => 255, 'null' => true])
            ->addIndex(['check_id'])
            ->addForeignKey('check_id', 'inventory_check', 'id', ['delete' => 'CASCADE'])
            ->addForeignKey('material_id', 'material', 'id')
            ->create();
    }
}
