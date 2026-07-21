<?php

namespace app\common\model;

class RolePermission extends Model
{
    protected $table = 'role_permission';

    public $timestamps = false;

    protected $fillable = ['role_id', 'permission_id'];
}
