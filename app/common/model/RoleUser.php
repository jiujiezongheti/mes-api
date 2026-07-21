<?php

namespace app\common\model;

class RoleUser extends Model
{
    protected $table = 'role_user';

    public $timestamps = false;

    protected $fillable = ['role_id', 'user_id'];
}
