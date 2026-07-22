<?php

use Phinx\Migration\AbstractMigration;

final class AddIsDefaultToBom extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('bom');
        $table->addColumn('is_default', 'boolean', ['default' => 0, 'comment' => '是否默认'])
            ->addIndex(['material_id', 'is_default'])
            ->update();
    }
}
