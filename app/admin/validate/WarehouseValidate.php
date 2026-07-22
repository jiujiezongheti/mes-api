<?php

namespace app\admin\validate;

use app\common\exceptions\BusinessException;
use app\common\ResponseCode;

class WarehouseValidate
{
    public static function create(array $data): void
    {
        if (empty($data['code']) || !is_string($data['code'])) {
            throw new BusinessException('请输入仓库编码', ResponseCode::ERROR_PARAM->value);
        }
        if (mb_strlen($data['code']) > 50) {
            throw new BusinessException('仓库编码不能超过50个字符', ResponseCode::ERROR_PARAM->value);
        }
        if (empty($data['name']) || !is_string($data['name'])) {
            throw new BusinessException('请输入仓库名称', ResponseCode::ERROR_PARAM->value);
        }
        if (mb_strlen($data['name']) > 100) {
            throw new BusinessException('仓库名称不能超过100个字符', ResponseCode::ERROR_PARAM->value);
        }
    }

    public static function update(array $data): void
    {
        if (empty($data['id'])) {
            throw new BusinessException('参数错误', ResponseCode::ERROR_PARAM->value);
        }
        if (isset($data['code']) && (empty($data['code']) || mb_strlen($data['code']) > 50)) {
            throw new BusinessException('仓库编码不合法', ResponseCode::ERROR_PARAM->value);
        }
    }
}
