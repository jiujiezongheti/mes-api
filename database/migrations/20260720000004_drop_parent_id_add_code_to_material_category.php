<?php

use Phinx\Migration\AbstractMigration;

class DropParentIdAddCodeToMaterialCategory extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('material_category');
        if ($table->hasColumn('parent_id')) {
            $table->removeColumn('parent_id');
        }
        if (!$table->hasColumn('code')) {
            $table->addColumn('code', 'string', ['limit' => 50, 'null' => true, 'default' => null, 'after' => 'name', 'comment' => '分类编码'])
                ->addIndex(['code'], ['unique' => true])
                ->update();
        }
    }
}
