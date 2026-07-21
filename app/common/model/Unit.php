<?php

namespace app\common\model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    protected $table = 'unit';

    protected $fillable = [
        'name', 'status', 'sort', 'remark', 'created_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort' => 'integer',
    ];

    public function materials()
    {
        return $this->hasMany(Material::class, 'unit_id');
    }
}
