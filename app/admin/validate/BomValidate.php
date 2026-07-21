<?php

namespace app\admin\validate;

use app\common\exceptions\BusinessException;
use app\common\ResponseCode;

class BomValidate
{
    public static function create(array $data): void
    {
        if (empty($data['code']) || !is_string($data['code'])) {
            throw new BusinessException('请输入BOM编号', ResponseCode::ERROR_PARAM->value);
        }
        if (mb_strlen($data['code']) > 50) {
            throw new BusinessException('BOM编号不能超过50个字符', ResponseCode::ERROR_PARAM->value);
        }
        if (empty($data['name']) || !is_string($data['name'])) {
            throw new BusinessException('请输入BOM名称', ResponseCode::ERROR_PARAM->value);
        }
        if (empty($data['material_id'])) {
            throw new BusinessException('请选择成品物料', ResponseCode::ERROR_PARAM->value);
        }
    }

    public static function update(array $data): void
    {
        if (empty($data['id'])) {
            throw new BusinessException('参数错误', ResponseCode::ERROR_PARAM->value);
        }
        if (isset($data['code']) && (empty($data['code']) || mb_strlen($data['code']) > 50)) {
            throw new BusinessException('BOM编号不合法', ResponseCode::ERROR_PARAM->value);
        }
    }

    public static function copy(array $data): void
    {
        if (empty($data['id'])) {
            throw new BusinessException('参数错误', ResponseCode::ERROR_PARAM->value);
        }
        if (empty($data['code']) || !is_string($data['code'])) {
            throw new BusinessException('请输入BOM编号', ResponseCode::ERROR_PARAM->value);
        }
        if (mb_strlen($data['code']) > 50) {
            throw new BusinessException('BOM编号不能超过50个字符', ResponseCode::ERROR_PARAM->value);
        }
        if (empty($data['name']) || !is_string($data['name'])) {
            throw new BusinessException('请输入BOM名称', ResponseCode::ERROR_PARAM->value);
        }
    }
}
