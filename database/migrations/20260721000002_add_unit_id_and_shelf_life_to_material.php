<?php

use Phinx\Migration\AbstractMigration;

class AddUnitIdAndShelfLifeToMaterial extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('material');

        if ($table->hasColumn('unit')) {
            $table->removeColumn('unit');
        }

        if (!$table->hasColumn('unit_id')) {
            $table->addColumn('unit_id', 'biginteger', ['signed' => false, 'null' => true, 'default' => null, 'after' => 'type', 'comment' => '计量单位ID'])
                ->addIndex(['unit_id'])
                ->update();
        }

        if (!$table->hasColumn('shelf_life_days')) {
            $table->addColumn('shelf_life_days', 'integer', ['null' => true, 'after' => 'status', 'comment' => '保质期（天）'])
                ->update();
        }

        if (!$table->hasColumn('is_expiry_controlled')) {
            $table->addColumn('is_expiry_controlled', 'boolean', ['default' => 0, 'after' => 'shelf_life_days', 'comment' => '是否启用有效期管理'])
                ->update();
        }
    }
}
