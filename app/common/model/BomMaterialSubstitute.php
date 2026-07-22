<?php

namespace app\common\model;

class BomMaterialSubstitute extends Model
{
    protected $table = 'bom_material_substitute';

    public $timestamps = false;

    protected $fillable = [
        'bom_material_id', 'material_id', 'priority', 'remark',
    ];

    protected $casts = [
        'priority' => 'integer',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function bomMaterial()
    {
        return $this->belongsTo(BomMaterial::class, 'bom_material_id');
    }
}
