<?php

use Phinx\Migration\AbstractMigration;

final class AddChildBomIdToBomMaterial extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('bom_material');
        $table->addColumn('child_bom_id', 'biginteger', ['signed' => false, 'null' => true, 'comment' => '子BOM ID'])
            ->addIndex(['child_bom_id'])
            ->update();
    }
}
