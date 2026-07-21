<?php

use Phinx\Migration\AbstractMigration;

final class AddLossRateToBomMaterial extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('bom_material');
        $table->addColumn('loss_rate', 'decimal', ['precision' => 5, 'scale' => 2, 'default' => 0, 'comment' => '损耗率(%)'])
            ->update();
    }
}
