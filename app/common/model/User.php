<?php

namespace app\common\model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    protected $table = 'user';

    protected $fillable = [
        'username', 'password', 'nickname', 'avatar',
        'phone', 'email', 'status', 'sort', 'remark',
    ];

    protected $hidden = [
        'password', 'deleted_at',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort' => 'integer',
    ];
}
