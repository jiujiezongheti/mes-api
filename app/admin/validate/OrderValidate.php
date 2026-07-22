<?php

namespace app\admin\validate;

use app\common\exceptions\BusinessException;
use app\common\ResponseCode;

class OrderValidate
{
    public static function create(array $data): void
    {
        if (empty($data['code'])) {
            throw new BusinessException('请输入工单编号', ResponseCode::ERROR_PARAM->value);
        }
        if (empty($data['material_id'])) {
            throw new BusinessException('请选择生产物料', ResponseCode::ERROR_PARAM->value);
        }
        if (!isset($data['quantity']) || (float)$data['quantity'] <= 0) {
            throw new BusinessException('计划数量必须大于0', ResponseCode::ERROR_PARAM->value);
        }
    }

    public static function update(array $data): void
    {
        if (empty($data['id'])) {
            throw new BusinessException('参数错误', ResponseCode::ERROR_PARAM->value);
        }
        if (isset($data['quantity']) && (float)$data['quantity'] <= 0) {
            throw new BusinessException('计划数量必须大于0', ResponseCode::ERROR_PARAM->value);
        }
    }

    public static function status(array $data): void
    {
        if (empty($data['id'])) {
            throw new BusinessException('参数错误', ResponseCode::ERROR_PARAM->value);
        }
        if (!isset($data['status']) || !in_array((int)$data['status'], [2, 3, 4])) {
            throw new BusinessException('状态不正确', ResponseCode::ERROR_PARAM->value);
        }
    }
}
