<?php

namespace app\common\model;

class ProductionOrderMaterial extends Model
{
    protected $table = 'production_order_material';

    public $timestamps = false;

    protected $fillable = [
        'order_id', 'material_id', 'required_quantity', 'issued_quantity', 'remark',
    ];

    protected $casts = [
        'required_quantity' => 'float',
        'issued_quantity' => 'float',
    ];

    public function order()
    {
        return $this->belongsTo(ProductionOrder::class, 'order_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
