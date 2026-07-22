<?php

namespace app\admin\logic;

use app\common\model\Material;
use app\common\exceptions\BusinessException;
use app\common\ResponseCode;
use app\common\utils\ExcelUtil;

class MaterialLogic
{
    public static function getList(array $params): array
    {
        $query = Material::with(['category', 'unit']);

        if (!empty($params['code'])) {
            $query->where('code', 'like', "%{$params['code']}%");
        }
        if (!empty($params['name'])) {
            $query->where('name', 'like', "%{$params['name']}%");
        }
        if (!empty($params['type'])) {
            if (is_string($params['type']) && str_contains($params['type'], ',')) {
                $types = array_map('intval', explode(',', $params['type']));
                $query->whereIn('type', $types);
            } else {
                $query->where('type', (int)$params['type']);
            }
        }
        if (isset($params['category_id']) && $params['category_id'] !== '') {
            $query->where('category_id', (int)$params['category_id']);
        }
        if (isset($params['unit_id']) && $params['unit_id'] !== '') {
            $query->where('unit_id', (int)$params['unit_id']);
        }
        if (isset($params['is_expiry_controlled']) && $params['is_expiry_controlled'] !== '') {
            $query->where('is_expiry_controlled', (int)$params['is_expiry_controlled']);
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
            ->get()
            ->map(function ($item) {
                $item->category_name = $item->category?->name;
                $item->category_code = $item->category?->code;
                $item->unit_name = $item->unit?->name;
                unset($item->category, $item->unit);
                return $item;
            });

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
        ];
    }

    public static function create(array $data): void
    {
        if (Material::where('code', $data['code'])->exists()) {
            throw new BusinessException('物料编码已存在', ResponseCode::ERROR_PARAM->value);
        }

        $material = new Material();
        $material->code = $data['code'];
        $material->name = $data['name'];
        $material->spec = $data['spec'] ?? null;
        $material->unit_id = $data['unit_id'] ?? null;
        $material->type = $data['type'] ?? 1;
        $material->category_id = $data['category_id'] ?? null;
        $material->shelf_life_days = $data['shelf_life_days'] ?? null;
        $material->is_expiry_controlled = $data['is_expiry_controlled'] ?? false;
        $material->status = $data['status'] ?? 1;
        $material->sort = $data['sort'] ?? 0;
        $material->remark = $data['remark'] ?? null;
        $material->created_by = $data['created_by'] ?? null;
        $material->save();
    }

    public static function update(array $data): void
    {
        $material = Material::find($data['id']);
        if (!$material) {
            throw new BusinessException('物料不存在', ResponseCode::ERROR_PARAM->value);
        }

        if (isset($data['code']) && $data['code'] !== $material->code) {
            if (Material::where('code', $data['code'])->exists()) {
                throw new BusinessException('物料编码已存在', ResponseCode::ERROR_PARAM->value);
            }
            $material->code = $data['code'];
        }

        if (isset($data['name'])) $material->name = $data['name'];
        if (isset($data['spec'])) $material->spec = $data['spec'];
        if (array_key_exists('unit_id', $data)) $material->unit_id = $data['unit_id'] ?: null;
        if (isset($data['type'])) $material->type = (int)$data['type'];
        if (array_key_exists('category_id', $data)) $material->category_id = $data['category_id'] ?: null;
        if (isset($data['shelf_life_days'])) $material->shelf_life_days = (int)$data['shelf_life_days'];
        if (isset($data['is_expiry_controlled'])) $material->is_expiry_controlled = (bool)$data['is_expiry_controlled'];
        if (isset($data['status'])) $material->status = (int)$data['status'];
        if (isset($data['sort'])) $material->sort = (int)$data['sort'];
        if (isset($data['remark'])) $material->remark = $data['remark'];
        $material->save();
    }

    public static function delete(int $id): void
    {
        $material = Material::find($id);
        if (!$material) {
            throw new BusinessException('物料不存在', ResponseCode::ERROR_PARAM->value);
        }
        $material->delete();
    }

    public static function export(?array $ids = null): string
    {
        $query = Material::with('unit');
        if ($ids !== null) {
            $query->whereIn('id', $ids);
        }
        $materials = $query->orderBy('id')->get();

        $typeMap = [1 => '原材料', 2 => '半成品', 3 => '成品', 4 => '辅料'];

        $headers = ['物料编码', '物料名称', '规格型号', '计量单位', '类型', '保质期(天)', '启用有效期', '状态', '排序', '备注'];
        $rows = $materials->map(function ($m) use ($typeMap) {
            return [
                $m->code,
                $m->name,
                $m->spec ?? '',
                $m->unit?->name ?? '',
                $typeMap[$m->type] ?? '未知',
                $m->shelf_life_days ?? '',
                $m->is_expiry_controlled ? '是' : '否',
                $m->status ? '启用' : '禁用',
                $m->sort,
                $m->remark ?? '',
            ];
        })->toArray();

        return ExcelUtil::export('物料', $headers, $rows);
    }

    public static function import(string $filePath): array
    {
        $data = ExcelUtil::import($filePath);
        $imported = 0;
        $errors = [];
        $typeMap = ['原材料' => 1, '半成品' => 2, '成品' => 3, '辅料' => 4];

        foreach ($data as $i => $row) {
            $line = $i + 2;
            $code = $row['物料编码'] ?? '';
            $name = $row['物料名称'] ?? '';
            if (empty($code) || empty($name)) {
                $errors[] = "第{$line}行：物料编码和物料名称不能为空";
                continue;
            }
            if (Material::where('code', $code)->exists()) {
                $errors[] = "第{$line}行：物料编码 '{$code}' 已存在";
                continue;
            }

            $type = $typeMap[$row['类型'] ?? ''] ?? 1;

            $material = new Material();
            $material->code = $code;
            $material->name = $name;
            $material->spec = $row['规格型号'] ?? null;
            $material->unit = $row['计量单位'] ?? null;
            $material->type = $type;
            $material->status = ($row['状态'] ?? '启用') === '启用' ? 1 : 0;
            $material->sort = (int)($row['排序'] ?? 0);
            $material->remark = $row['备注'] ?? null;
            $material->save();
            $imported++;
        }

        return ['imported' => $imported, 'errors' => $errors];
    }
}
