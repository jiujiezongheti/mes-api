<?php

namespace app\common\model;

class Permission extends Model
{
    protected $table = 'permission';

    public $timestamps = false;

    protected $fillable = [
        'parent_id', 'name', 'code', 'type', 'sort',
    ];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id')->orderBy('sort');
    }
}
