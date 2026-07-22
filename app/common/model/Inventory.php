<?php

namespace app\common\model;

class Inventory extends Model
{
    protected $table = 'inventory';

    public $timestamps = false;

    protected $fillable = [
        'warehouse_id', 'material_id', 'quantity', 'locked_quantity',
    ];

    protected $casts = [
        'quantity' => 'float',
        'locked_quantity' => 'float',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
