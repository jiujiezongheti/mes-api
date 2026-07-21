<?php

namespace app\admin\validate;

use app\common\exceptions\BusinessException;
use app\common\ResponseCode;

class UnitValidate
{
    public static function create(array $data): void
    {
        if (empty($data['name']) || !is_string($data['name'])) {
            throw new BusinessException('请输入单位名称', ResponseCode::ERROR_PARAM->value);
        }
        if (mb_strlen($data['name']) > 30) {
            throw new BusinessException('单位名称不能超过30个字符', ResponseCode::ERROR_PARAM->value);
        }
    }

    public static function update(array $data): void
    {
        if (empty($data['id'])) {
            throw new BusinessException('参数错误', ResponseCode::ERROR_PARAM->value);
        }
        if (isset($data['name']) && (empty($data['name']) || mb_strlen($data['name']) > 30)) {
            throw new BusinessException('单位名称不合法', ResponseCode::ERROR_PARAM->value);
        }
    }
}
