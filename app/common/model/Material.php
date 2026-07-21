<?php

namespace app\common\model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use SoftDeletes;

    protected $table = 'material';

    protected $fillable = [
        'code', 'name', 'spec', 'unit_id', 'type', 'category_id', 'shelf_life_days', 'is_expiry_controlled', 'status', 'sort', 'remark', 'created_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'type' => 'integer',
        'sort' => 'integer',
        'category_id' => 'integer',
        'unit_id' => 'integer',
        'shelf_life_days' => 'integer',
        'is_expiry_controlled' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(MaterialCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
