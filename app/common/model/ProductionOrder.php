<?php

namespace app\common\model;

use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionOrder extends Model
{
    use SoftDeletes;

    protected $table = 'production_order';

    protected $fillable = [
        'code', 'bom_id', 'material_id', 'quantity', 'produced_quantity',
        'status', 'priority', 'plan_start_date', 'plan_end_date',
        'actual_start_date', 'actual_end_date', 'sort', 'remark', 'created_by',
    ];

    protected $casts = [
        'quantity' => 'float',
        'produced_quantity' => 'float',
        'status' => 'integer',
        'priority' => 'integer',
        'sort' => 'integer',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function bom()
    {
        return $this->belongsTo(Bom::class, 'bom_id');
    }

    public function materials()
    {
        return $this->hasMany(ProductionOrderMaterial::class, 'order_id');
    }
}
