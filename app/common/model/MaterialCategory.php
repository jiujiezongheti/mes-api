<?php

namespace app\common\model;

use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialCategory extends Model
{
    use SoftDeletes;

    protected $table = 'material_category';

    protected $fillable = [
        'code', 'name', 'status', 'sort', 'remark', 'created_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort' => 'integer',
    ];

    public function materials()
    {
        return $this->hasMany(Material::class, 'category_id');
    }
}
