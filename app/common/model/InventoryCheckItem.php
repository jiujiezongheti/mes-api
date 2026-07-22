<?php

namespace app\common\model;

class InventoryCheckItem extends Model
{
    protected $table = 'inventory_check_item';

    public $timestamps = false;

    protected $fillable = [
        'check_id', 'material_id', 'book_quantity', 'actual_quantity', 'remark',
    ];

    protected $casts = [
        'book_quantity' => 'float',
        'actual_quantity' => 'float',
    ];

    public function check()
    {
        return $this->belongsTo(InventoryCheck::class, 'check_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
