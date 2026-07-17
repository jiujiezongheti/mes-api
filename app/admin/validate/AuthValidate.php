<?php

namespace app\admin\validate;

use app\common\exceptions\BusinessException;
use app\common\ResponseCode;

class AuthValidate
{
    public static function login(array $data): void
    {
        if (empty($data['username']) || !is_string($data['username'])) {
            throw new BusinessException('请输入账号', ResponseCode::ERROR_PARAM->value);
        }
        if (empty($data['password']) || !is_string($data['password'])) {
            throw new BusinessException('请输入密码', ResponseCode::ERROR_PARAM->value);
        }
    }
}
