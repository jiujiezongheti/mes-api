<?php

use Phinx\Migration\AbstractMigration;

final class ChangeBomCodeIndex extends AbstractMigration
{
    public function change(): void
    {
        $this->table('bom')
            ->removeIndex(['code'])
            ->addIndex(['code'])
            ->update();
    }
}
