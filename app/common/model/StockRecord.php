<?php

namespace app\common\model;

class StockRecord extends Model
{
    protected $table = 'stock_record';

    public $timestamps = false;

    protected $fillable = [
        'warehouse_id', 'material_id', 'type', 'quantity',
        'before_quantity', 'after_quantity',
        'source_type', 'source_id', 'remark', 'created_by',
    ];

    protected $casts = [
        'type' => 'integer',
        'quantity' => 'float',
        'before_quantity' => 'float',
        'after_quantity' => 'float',
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
