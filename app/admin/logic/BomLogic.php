<?php

namespace app\admin\logic;

use app\common\model\Bom;
use app\common\model\BomMaterial;
use app\common\model\BomMaterialSubstitute;
use app\common\model\Material;
use app\common\exceptions\BusinessException;
use app\common\ResponseCode;
use app\common\utils\ExcelUtil;

class BomLogic
{
    public static function getList(array $params): array
    {
        $query = Bom::with(['material' => function ($q) {
            $q->select('id', 'code', 'name');
        }]);

        if (!empty($params['code'])) {
            $query->where('code', 'like', "%{$params['code']}%");
        }
        if (!empty($params['name'])) {
            $query->where('name', 'like', "%{$params['name']}%");
        }
        if (!empty($params['material_code'])) {
            $query->whereHas('material', function ($q) use ($params) {
                $q->where('code', 'like', "%{$params['material_code']}%");
            });
        }
        if (!empty($params['material_name'])) {
            $query->whereHas('material', function ($q) use ($params) {
                $q->where('name', 'like', "%{$params['material_name']}%");
            });
        }
        if (isset($params['quantity']) && $params['quantity'] !== '') {
            $query->where('quantity', (float)$params['quantity']);
        }
        if (isset($params['sort']) && $params['sort'] !== '') {
            $query->where('sort', (int)$params['sort']);
        }
        if (!empty($params['remark'])) {
            $query->where('remark', 'like', "%{$params['remark']}%");
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', (int)$params['status']);
        }
        if (!empty($params['material_id'])) {
            $query->where('material_id', (int)$params['material_id']);
        }

        $page = (int)($params['page'] ?? 1);
        $pageSize = (int)($params['pageSize'] ?? 20);

        $total = $query->count();
        $list = $query->orderBy('sort')->orderBy('id', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->map(function ($item) {
                $item->material_name = $item->material?->name;
                $item->material_code = $item->material?->code;
                unset($item->material);
                return $item;
            });

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
        ];
    }

    public static function detail(int $id): array
    {
        $bom = Bom::with('materials.material', 'materials.childBom', 'materials.substitutes.material')->find($id);
        if (!$bom) {
            throw new BusinessException('BOM不存在', ResponseCode::ERROR_PARAM->value);
        }

        $data = $bom->toArray();
        $data['material_name'] = $bom->material?->name;
        $data['material_code'] = $bom->material?->code;

        foreach ($data['materials'] as &$m) {
            $m['material_name'] = $m['material']['name'] ?? '';
            $m['material_code'] = $m['material']['code'] ?? '';
            unset($m['material']);
            if (isset($m['child_bom'])) {
                $m['child_bom_code'] = $m['child_bom']['code'] ?? '';
                $m['child_bom_name'] = $m['child_bom']['name'] ?? '';
                unset($m['child_bom']);
            }
            if (isset($m['substitutes'])) {
                foreach ($m['substitutes'] as &$s) {
                    $s['material_name'] = $s['material']['name'] ?? '';
                    $s['material_code'] = $s['material']['code'] ?? '';
                    unset($s['material']);
                }
            }
        }

        return $data;
    }

    public static function create(array $data): void
    {
        if (Bom::where('code', $data['code'])->exists()) {
            throw new BusinessException('BOM编号已存在', ResponseCode::ERROR_PARAM->value);
        }

        $hasExisting = Bom::where('material_id', $data['material_id'])->exists();
        $isDefault = !$hasExisting || !empty($data['is_default']);
        if ($isDefault && $hasExisting) {
            Bom::where('material_id', $data['material_id'])->update(['is_default' => 0]);
        }

        $bom = new Bom();
        $bom->code = $data['code'];
        $bom->name = $data['name'];
        $bom->material_id = $data['material_id'];
        $bom->quantity = $data['quantity'] ?? 1;
        $bom->status = $data['status'] ?? 1;
        $bom->is_default = $isDefault;
        $bom->sort = $data['sort'] ?? 0;
        $bom->remark = $data['remark'] ?? null;
        $bom->created_by = $data['created_by'] ?? null;
        $bom->save();

        if (!empty($data['materials'])) {
            self::saveMaterials($bom->id, $data['materials']);
        }
    }

    public static function update(array $data): void
    {
        $bom = Bom::find($data['id']);
        if (!$bom) {
            throw new BusinessException('BOM不存在', ResponseCode::ERROR_PARAM->value);
        }

        if (isset($data['code']) && $data['code'] !== $bom->code) {
            if (Bom::where('code', $data['code'])->exists()) {
                throw new BusinessException('BOM编号已存在', ResponseCode::ERROR_PARAM->value);
            }
            $bom->code = $data['code'];
        }

        if (isset($data['name'])) $bom->name = $data['name'];
        if (isset($data['material_id'])) $bom->material_id = $data['material_id'];
        if (isset($data['quantity'])) $bom->quantity = $data['quantity'];
        if (isset($data['status'])) $bom->status = (int)$data['status'];
        if (isset($data['is_default']) && !empty($data['is_default'])) {
            Bom::where('material_id', $bom->material_id)->where('id', '!=', $bom->id)->update(['is_default' => 0]);
            $bom->is_default = 1;
        } elseif (isset($data['is_default'])) {
            $bom->is_default = 0;
        }
        if (isset($data['sort'])) $bom->sort = (int)$data['sort'];
        if (isset($data['remark'])) $bom->remark = $data['remark'];
        $bom->save();

        if (isset($data['materials'])) {
            BomMaterial::where('bom_id', $bom->id)->delete();
            self::saveMaterials($bom->id, $data['materials']);
        }
    }

    public static function delete(int $id): void
    {
        $bom = Bom::find($id);
        if (!$bom) {
            throw new BusinessException('BOM不存在', ResponseCode::ERROR_PARAM->value);
        }
        BomMaterial::where('bom_id', $id)->delete();
        $bom->delete();
    }

    public static function export(?array $ids = null): string
    {
        $query = Bom::with('material:id,code,name');
        if ($ids !== null) {
            $query->whereIn('id', $ids);
        }
        $list = $query->orderBy('id')->get();

        $headers = ['BOM编号', 'BOM名称', '成品编码', '成品名称', '产出数量', '排序', '是否默认', '状态', '备注'];
        $rows = $list->map(function ($item) {
            return [
                $item->code,
                $item->name,
                $item->material?->code ?? '',
                $item->material?->name ?? '',
                $item->quantity,
                $item->sort,
                $item->is_default ? '是' : '否',
                $item->status ? '启用' : '禁用',
                $item->remark ?? '',
            ];
        })->toArray();

        return ExcelUtil::export('BOM数据', $headers, $rows);
    }

    public static function import(string $filePath): array
    {
        $data = ExcelUtil::import($filePath);
        $imported = 0;
        $errors = [];

        foreach ($data as $i => $row) {
            $line = $i + 2;
            $code = $row['BOM编号'] ?? '';
            if (empty($code)) {
                $errors[] = "第{$line}行：BOM编号不能为空";
                continue;
            }
            if (Bom::where('code', $code)->exists()) {
                $errors[] = "第{$line}行：BOM编号【{$code}】已存在";
                continue;
            }

            $name = $row['BOM名称'] ?? '';
            if (empty($name)) {
                $errors[] = "第{$line}行：BOM名称不能为空";
                continue;
            }

            $materialCode = $row['成品编码'] ?? '';
            $materialId = null;
            if (!empty($materialCode)) {
                $material = Material::where('code', $materialCode)->first();
                if (!$material) {
                    $errors[] = "第{$line}行：成品编码【{$materialCode}】不存在";
                    continue;
                }
                $materialId = $material->id;
            }

            $isDefault = ($row['是否默认'] ?? '否') === '是';

            $bom = new Bom();
            $bom->code = $code;
            $bom->name = $name;
            $bom->material_id = $materialId;
            $bom->quantity = (float)($row['产出数量'] ?? 1);
            $bom->sort = (int)($row['排序'] ?? 0);
            $bom->is_default = $isDefault;
            $bom->status = ($row['状态'] ?? '启用') === '启用' ? 1 : 0;
            $bom->remark = $row['备注'] ?? null;
            $bom->save();

            if ($isDefault) {
                Bom::where('material_id', $materialId)->where('id', '!=', $bom->id)->update(['is_default' => 0]);
            }
            $imported++;
        }

        return ['imported' => $imported, 'errors' => $errors];
    }

    public static function tree(int $id, array $visited = []): array
    {
        $bom = Bom::with('materials.material')->find($id);
        if (!$bom) {
            throw new BusinessException('BOM不存在', ResponseCode::ERROR_PARAM->value);
        }

        if (in_array($id, $visited)) {
            return [
                'id' => $bom->id,
                'type' => 'bom',
                'code' => $bom->code,
                'name' => $bom->name . '(循环引用)',
                'quantity' => $bom->quantity,
                'material_code' => $bom->material?->code ?? '',
                'material_name' => $bom->material?->name ?? '',
                'children' => [],
                '_cycle' => true,
            ];
        }
        $visited[] = $id;

        $node = [
            'id' => $bom->id,
            'type' => 'bom',
            'code' => $bom->code,
            'name' => $bom->name,
            'quantity' => $bom->quantity,
            'material_code' => $bom->material?->code ?? '',
            'material_name' => $bom->material?->name ?? '',
            'children' => [],
        ];

        foreach ($bom->materials as $item) {
            $child = [
                'id' => $item->material_id,
                'type' => 'material',
                'code' => $item->material?->code ?? '',
                'name' => $item->material?->name ?? '',
                'quantity' => $item->quantity,
                'loss_rate' => $item->loss_rate ?? 0,
                'remark' => $item->remark ?? '',
                'bom_id' => $item->bom_id,
                'child_bom_id' => $item->child_bom_id,
                'children' => [],
            ];

            if ($item->child_bom_id && !in_array($item->child_bom_id, $visited)) {
                $subNode = self::tree($item->child_bom_id, $visited);
                $subNode['quantity'] = $item->quantity;
                $child['children'] = [$subNode];
            }

            $node['children'][] = $child;
        }

        return $node;
    }

    public static function whereUsed(int $materialId): array
    {
        $items = BomMaterial::with('bom.material')
            ->where('material_id', $materialId)
            ->get()
            ->map(function ($bm) {
                return [
                    'bom_id' => $bm->bom_id,
                    'bom_code' => $bm->bom?->code ?? '',
                    'bom_name' => $bm->bom?->name ?? '',
                    'quantity' => $bm->quantity,
                    'parent_material_name' => $bm->bom?->material?->name ?? '',
                    'parent_material_code' => $bm->bom?->material?->code ?? '',
                ];
            });

        return $items->toArray();
    }

    public static function copy(int $id, string $newCode, string $newName, ?int $materialId = null): void
    {
        if (Bom::where('code', $newCode)->exists()) {
            throw new BusinessException('BOM编号已存在', ResponseCode::ERROR_PARAM->value);
        }

        $bom = Bom::with('materials.substitutes')->find($id);
        if (!$bom) {
            throw new BusinessException('BOM不存在', ResponseCode::ERROR_PARAM->value);
        }

        $newBom = new Bom();
        $newBom->code = $newCode;
        $newBom->name = $newName;
        $newBom->material_id = $materialId ?? $bom->material_id;
        $newBom->quantity = $bom->quantity;
        $newBom->status = 1;
        $newBom->is_default = 0;
        $newBom->sort = $bom->sort;
        $newBom->remark = $bom->remark;
        $newBom->save();

        foreach ($bom->materials as $m) {
            $newBm = BomMaterial::create([
                'bom_id' => $newBom->id,
                'material_id' => $m->material_id,
                'quantity' => $m->quantity,
                'loss_rate' => $m->loss_rate ?? 0,
                'child_bom_id' => $m->child_bom_id,
                'sort' => $m->sort,
                'remark' => $m->remark,
            ]);
            foreach ($m->substitutes as $sub) {
                BomMaterialSubstitute::create([
                    'bom_material_id' => $newBm->id,
                    'material_id' => $sub->material_id,
                    'priority' => $sub->priority,
                    'remark' => $sub->remark,
                ]);
            }
        }
    }

    private static function saveMaterials(int $bomId, array $materials): void
    {
        $sort = 0;
        foreach ($materials as $item) {
            if (empty($item['material_id'])) continue;
            $bm = BomMaterial::create([
                'bom_id' => $bomId,
                'material_id' => $item['material_id'],
                'quantity' => $item['quantity'] ?? 1,
                'loss_rate' => $item['loss_rate'] ?? 0,
                'child_bom_id' => !empty($item['child_bom_id']) ? (int)$item['child_bom_id'] : null,
                'sort' => $item['sort'] ?? $sort,
                'remark' => $item['remark'] ?? null,
            ]);
            if (!empty($item['substitutes'])) {
                foreach ($item['substitutes'] as $sub) {
                    if (empty($sub['material_id'])) continue;
                    BomMaterialSubstitute::create([
                        'bom_material_id' => $bm->id,
                        'material_id' => $sub['material_id'],
                        'priority' => $sub['priority'] ?? 0,
                        'remark' => $sub['remark'] ?? null,
                    ]);
                }
            }
            $sort++;
        }
    }
}
