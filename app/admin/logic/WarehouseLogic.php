<?php

namespace app\admin\logic;

use app\common\model\Warehouse;
use app\common\exceptions\BusinessException;
use app\common\ResponseCode;

class WarehouseLogic
{
    public static function getList(array $params): array
    {
        $query = Warehouse::query();

        if (!empty($params['code'])) {
            $query->where('code', 'like', "%{$params['code']}%");
        }
        if (!empty($params['name'])) {
            $query->where('name', 'like', "%{$params['name']}%");
        }
        if (isset($params['type']) && $params['type'] !== '') {
            $query->where('type', (int)$params['type']);
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', (int)$params['status']);
        }

        $page = (int)($params['page'] ?? 1);
        $pageSize = (int)($params['pageSize'] ?? 20);
        $total = $query->count();
        $list = $query->orderBy('sort')->orderBy('id', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        return ['list' => $list, 'total' => $total, 'page' => $page, 'pageSize' => $pageSize];
    }

    public static function all(): array
    {
        return Warehouse::where('status', 1)->orderBy('sort')->get()->toArray();
    }

    public static function create(array $data): void
    {
        if (Warehouse::where('code', $data['code'])->exists()) {
            throw new BusinessException('仓库编码已存在', ResponseCode::ERROR_PARAM->value);
        }
        $wh = new Warehouse();
        $wh->code = $data['code'];
        $wh->name = $data['name'];
        $wh->type = $data['type'] ?? 1;
        $wh->address = $data['address'] ?? null;
        $wh->status = $data['status'] ?? 1;
        $wh->sort = $data['sort'] ?? 0;
        $wh->remark = $data['remark'] ?? null;
        $wh->created_by = $data['created_by'] ?? null;
        $wh->save();
    }

    public static function update(array $data): void
    {
        $wh = Warehouse::find($data['id']);
        if (!$wh) {
            throw new BusinessException('仓库不存在', ResponseCode::ERROR_PARAM->value);
        }
        if (isset($data['code']) && $data['code'] !== $wh->code) {
            if (Warehouse::where('code', $data['code'])->exists()) {
                throw new BusinessException('仓库编码已存在', ResponseCode::ERROR_PARAM->value);
            }
            $wh->code = $data['code'];
        }
        if (isset($data['name'])) $wh->name = $data['name'];
        if (isset($data['type'])) $wh->type = (int)$data['type'];
        if (isset($data['address'])) $wh->address = $data['address'];
        if (isset($data['status'])) $wh->status = (int)$data['status'];
        if (isset($data['sort'])) $wh->sort = (int)$data['sort'];
        if (isset($data['remark'])) $wh->remark = $data['remark'];
        $wh->save();
    }

    public static function delete(int $id): void
    {
        $wh = Warehouse::find($id);
        if (!$wh) {
            throw new BusinessException('仓库不存在', ResponseCode::ERROR_PARAM->value);
        }
        $wh->delete();
    }
}
