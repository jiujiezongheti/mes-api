<?php

namespace app\admin\validate;

use app\common\exceptions\BusinessException;
use app\common\ResponseCode;

class MaterialCategoryValidate
{
    public static function create(array $data): void
    {
        if (empty($data['name']) || !is_string($data['name'])) {
            throw new BusinessException('请输入分类名称', ResponseCode::ERROR_PARAM->value);
        }
        if (mb_strlen($data['name']) > 50) {
            throw new BusinessException('分类名称不能超过50个字符', ResponseCode::ERROR_PARAM->value);
        }
        if (empty($data['code']) || !is_string($data['code'])) {
            throw new BusinessException('请输入分类编码', ResponseCode::ERROR_PARAM->value);
        }
        if (mb_strlen($data['code']) > 50) {
            throw new BusinessException('分类编码不能超过50个字符', ResponseCode::ERROR_PARAM->value);
        }
    }

    public static function update(array $data): void
    {
        if (empty($data['id'])) {
            throw new BusinessException('参数错误', ResponseCode::ERROR_PARAM->value);
        }
        if (isset($data['name']) && (empty($data['name']) || mb_strlen($data['name']) > 50)) {
            throw new BusinessException('分类名称不合法', ResponseCode::ERROR_PARAM->value);
        }
        if (isset($data['code']) && (empty($data['code']) || mb_strlen($data['code']) > 50)) {
            throw new BusinessException('分类编码不合法', ResponseCode::ERROR_PARAM->value);
        }
    }
}
