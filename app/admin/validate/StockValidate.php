<?php

namespace app\admin\validate;

use app\common\exceptions\BusinessException;
use app\common\ResponseCode;

class StockValidate
{
    public static function in(array $data): void
    {
        if (empty($data['warehouse_id'])) {
            throw new BusinessException('请选择仓库', ResponseCode::ERROR_PARAM->value);
        }
        if (empty($data['material_id'])) {
            throw new BusinessException('请选择物料', ResponseCode::ERROR_PARAM->value);
        }
        if (!isset($data['quantity']) || (float)$data['quantity'] <= 0) {
            throw new BusinessException('入库数量必须大于0', ResponseCode::ERROR_PARAM->value);
        }
    }

    public static function out(array $data): void
    {
        if (empty($data['warehouse_id'])) {
            throw new BusinessException('请选择仓库', ResponseCode::ERROR_PARAM->value);
        }
        if (empty($data['material_id'])) {
            throw new BusinessException('请选择物料', ResponseCode::ERROR_PARAM->value);
        }
        if (!isset($data['quantity']) || (float)$data['quantity'] <= 0) {
            throw new BusinessException('出库数量必须大于0', ResponseCode::ERROR_PARAM->value);
        }
    }

    public static function checkCreate(array $data): void
    {
        if (empty($data['warehouse_id'])) {
            throw new BusinessException('请选择仓库', ResponseCode::ERROR_PARAM->value);
        }
    }

    public static function checkComplete(array $data): void
    {
        if (empty($data['id'])) {
            throw new BusinessException('参数错误', ResponseCode::ERROR_PARAM->value);
        }
        if (empty($data['items']) || !is_array($data['items'])) {
            throw new BusinessException('请录入盘点明细', ResponseCode::ERROR_PARAM->value);
        }
    }
}
