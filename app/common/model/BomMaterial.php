<?php

namespace app\common\model;

class BomMaterial extends Model
{
    protected $table = 'bom_material';

    public $timestamps = false;

    protected $fillable = [
        'bom_id', 'material_id', 'quantity', 'loss_rate', 'child_bom_id', 'sort', 'remark',
    ];

    protected $casts = [
        'quantity' => 'float',
        'loss_rate' => 'float',
        'sort' => 'integer',
        'child_bom_id' => 'integer',
    ];

    public function bom()
    {
        return $this->belongsTo(Bom::class, 'bom_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function childBom()
    {
        return $this->belongsTo(Bom::class, 'child_bom_id');
    }

    public function substitutes()
    {
        return $this->hasMany(BomMaterialSubstitute::class, 'bom_material_id');
    }
}
