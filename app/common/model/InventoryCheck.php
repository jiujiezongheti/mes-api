<?php

namespace app\common\model;

class InventoryCheck extends Model
{
    protected $table = 'inventory_check';

    protected $fillable = [
        'code', 'warehouse_id', 'status', 'remark', 'created_by',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function items()
    {
        return $this->hasMany(InventoryCheckItem::class, 'check_id');
    }
}
