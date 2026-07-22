<?php

namespace app\common\model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;

    protected $table = 'warehouse';

    protected $fillable = [
        'code', 'name', 'type', 'address', 'status', 'sort', 'remark', 'created_by',
    ];

    protected $casts = [
        'type' => 'integer',
        'status' => 'boolean',
        'sort' => 'integer',
    ];

    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'warehouse_id');
    }
}
