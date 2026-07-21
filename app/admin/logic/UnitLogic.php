<?php

namespace app\admin\logic;

use app\common\model\Unit;
use app\common\exceptions\BusinessException;
use app\common\ResponseCode;
use app\common\utils\ExcelUtil;

class UnitLogic
{
    public static function getList(array $params): array
    {
        $query = Unit::query();

        if (!empty($params['name'])) {
            $query->where('name', 'like', "%{$params['name']}%");
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

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
        ];
    }

    public static function getAll(): array
    {
        return Unit::orderBy('sort')->orderBy('id')->get()->toArray();
    }

    public static function create(array $data): void
    {
        $unit = new Unit();
        $unit->name = $data['name'];
        $unit->status = $data['status'] ?? 1;
        $unit->sort = $data['sort'] ?? 0;
        $unit->remark = $data['remark'] ?? null;
        $unit->created_by = $data['created_by'] ?? null;
        $unit->save();
    }

    public static function update(array $data): void
    {
        $unit = Unit::find($data['id']);
        if (!$unit) {
            throw new BusinessException('单位不存在', ResponseCode::ERROR_PARAM->value);
        }

        if (isset($data['name'])) $unit->name = $data['name'];
        if (isset($data['status'])) $unit->status = (int)$data['status'];
        if (isset($data['sort'])) $unit->sort = (int)$data['sort'];
        if (isset($data['remark'])) $unit->remark = $data['remark'];
        $unit->save();
    }

    public static function delete(int $id): void
    {
        $unit = Unit::find($id);
        if (!$unit) {
            throw new BusinessException('单位不存在', ResponseCode::ERROR_PARAM->value);
        }

        if ($unit->materials()->exists()) {
            throw new BusinessException('该单位下有物料，无法删除', ResponseCode::ERROR_PARAM->value);
        }

        $unit->delete();
    }

    public static function export(?array $ids = null): string
    {
        $query = Unit::query();
        if ($ids !== null) {
            $query->whereIn('id', $ids);
        }
        $units = $query->orderBy('id')->get();

        $headers = ['单位名称', '排序', '状态', '备注'];
        $rows = $units->map(function ($u) {
            return [
                $u->name,
                $u->sort,
                $u->status ? '启用' : '禁用',
                $u->remark ?? '',
            ];
        })->toArray();

        return ExcelUtil::export('计量单位', $headers, $rows);
    }

    public static function import(string $filePath): array
    {
        $data = ExcelUtil::import($filePath);
        $imported = 0;
        $errors = [];

        foreach ($data as $i => $row) {
            $line = $i + 2;
            $name = $row['单位名称'] ?? '';
            if (empty($name)) {
                $errors[] = "第{$line}行：单位名称不能为空";
                continue;
            }

            $unit = new Unit();
            $unit->name = $name;
            $unit->sort = (int)($row['排序'] ?? 0);
            $unit->status = ($row['状态'] ?? '启用') === '启用' ? 1 : 0;
            $unit->remark = $row['备注'] ?? null;
            $unit->save();
            $imported++;
        }

        return ['imported' => $imported, 'errors' => $errors];
    }
}
