<?php

namespace app\common\model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Bom extends Model
{
    use SoftDeletes;

    protected $table = 'bom';

    protected $fillable = [
        'code', 'name', 'material_id', 'quantity', 'status', 'is_default', 'sort', 'remark', 'created_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_default' => 'boolean',
        'sort' => 'integer',
        'quantity' => 'float',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function materials()
    {
        return $this->hasMany(BomMaterial::class, 'bom_id');
    }
}
