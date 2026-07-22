<?php

use Phinx\Migration\AbstractMigration;

final class CreateBomMaterialSubstituteTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('bom_material_substitute', ['id' => false, 'primary_key' => 'id']);
        $table->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('bom_material_id', 'biginteger', ['signed' => false, 'comment' => 'BOM物料明细ID'])
            ->addColumn('material_id', 'biginteger', ['signed' => false, 'comment' => '替代物料ID'])
            ->addColumn('priority', 'integer', ['default' => 0, 'comment' => '优先级(数字越小越优先)'])
            ->addColumn('remark', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addForeignKey('bom_material_id', 'bom_material', 'id', ['delete' => 'CASCADE'])
            ->addIndex(['bom_material_id', 'material_id'], ['unique' => true])
            ->create();
    }
}
