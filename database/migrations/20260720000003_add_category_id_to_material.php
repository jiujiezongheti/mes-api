<?php

use Phinx\Migration\AbstractMigration;

class AddCategoryIdToMaterial extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('material');
        if (!$table->hasColumn('category_id')) {
            $table->addColumn('category_id', 'biginteger', ['signed' => false, 'null' => true, 'default' => null, 'after' => 'type', 'comment' => '物料分类ID'])
                ->addIndex(['category_id'])
                ->update();
        }
    }
}
