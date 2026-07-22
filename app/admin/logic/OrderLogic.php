<?php

namespace app\admin\logic;

use app\common\model\ProductionOrder;
use app\common\model\ProductionOrderMaterial;
use app\common\model\Bom;
use app\common\model\BomMaterial;
use app\common\model\Material;
use app\common\exceptions\BusinessException;
use app\common\ResponseCode;
use Illuminate\Database\Capsule\Manager as DB;

class OrderLogic
{
    public static function getList(array $params): array
    {
        $query = ProductionOrder::with([
            'material:id,code,name,spec',
            'bom:id,code,name',
        ]);

        if (!empty($params['code'])) {
            $query->where('code', 'like', "%{$params['code']}%");
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
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', (int)$params['status']);
        }
        if (!empty($params['start_date'])) {
            $query->where('created_at', '>=', $params['start_date']);
        }
        if (!empty($params['end_date'])) {
            $query->where('created_at', '<=', $params['end_date'] . ' 23:59:59');
        }

        $page = (int)($params['page'] ?? 1);
        $pageSize = (int)($params['pageSize'] ?? 20);
        $total = $query->count();
        $list = $query->orderBy('id', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->map(function ($item) {
                $statusMap = [1 => '待生产', 2 => '生产中', 3 => '已完成', 4 => '已关闭'];
                $priorityMap = [1 => '普通', 2 => '紧急'];
                return [
                    'id' => $item->id,
                    'code' => $item->code,
                    'bom_id' => $item->bom_id,
                    'bom_code' => $item->bom?->code ?? '',
                    'bom_name' => $item->bom?->name ?? '',
                    'material_id' => $item->material_id,
                    'material_code' => $item->material?->code ?? '',
                    'material_name' => $item->material?->name ?? '',
                    'material_spec' => $item->material?->spec ?? '',
                    'quantity' => (float)$item->quantity,
                    'produced_quantity' => (float)$item->produced_quantity,
                    'status' => $item->status,
                    'status_name' => $statusMap[$item->status] ?? '未知',
                    'priority' => $item->priority,
                    'priority_name' => $priorityMap[$item->priority] ?? '未知',
                    'plan_start_date' => $item->plan_start_date,
                    'plan_end_date' => $item->plan_end_date,
                    'actual_start_date' => $item->actual_start_date,
                    'actual_end_date' => $item->actual_end_date,
                    'sort' => $item->sort,
                    'remark' => $item->remark ?? '',
                    'created_by' => $item->created_by,
                    'created_at' => $item->created_at,
                ];
            });

        return ['list' => $list, 'total' => $total, 'page' => $page, 'pageSize' => $pageSize];
    }

    public static function detail(int $id): array
    {
        $order = ProductionOrder::with([
            'material:id,code,name,spec',
            'bom:id,code,name',
            'materials' => function ($q) {
                $q->with('material:id,code,name,spec');
            },
        ])->find($id);

        if (!$order) {
            throw new BusinessException('工单不存在', ResponseCode::ERROR_PARAM->value);
        }

        return [
            'order' => [
                'id' => $order->id,
                'code' => $order->code,
                'bom_id' => $order->bom_id,
                'material_id' => $order->material_id,
                'material_code' => $order->material?->code ?? '',
                'material_name' => $order->material?->name ?? '',
                'quantity' => (float)$order->quantity,
                'produced_quantity' => (float)$order->produced_quantity,
                'status' => $order->status,
                'priority' => $order->priority,
                'plan_start_date' => $order->plan_start_date,
                'plan_end_date' => $order->plan_end_date,
                'actual_start_date' => $order->actual_start_date,
                'actual_end_date' => $order->actual_end_date,
                'sort' => $order->sort,
                'remark' => $order->remark ?? '',
            ],
            'materials' => $order->materials->map(function ($m) {
                return [
                    'id' => $m->id,
                    'material_id' => $m->material_id,
                    'material_code' => $m->material?->code ?? '',
                    'material_name' => $m->material?->name ?? '',
                    'material_spec' => $m->material?->spec ?? '',
                    'required_quantity' => (float)$m->required_quantity,
                    'issued_quantity' => (float)$m->issued_quantity,
                    'remark' => $m->remark ?? '',
                ];
            }),
        ];
    }

    public static function create(array $data): void
    {
        DB::transaction(function () use ($data) {
            // Validate material exists
            $material = Material::find($data['material_id']);
            if (!$material) {
                throw new BusinessException('物料不存在', ResponseCode::ERROR_PARAM->value);
            }

            // Validate BOM if provided
            if (!empty($data['bom_id'])) {
                $bom = Bom::with('materials')->find($data['bom_id']);
                if (!$bom) {
                    throw new BusinessException('BOM不存在', ResponseCode::ERROR_PARAM->value);
                }
            }

            $order = ProductionOrder::create([
                'code' => $data['code'],
                'bom_id' => $data['bom_id'] ?? null,
                'material_id' => $data['material_id'],
                'quantity' => $data['quantity'],
                'produced_quantity' => 0,
                'status' => 1,
                'priority' => $data['priority'] ?? 1,
                'plan_start_date' => $data['plan_start_date'] ?? null,
                'plan_end_date' => $data['plan_end_date'] ?? null,
                'sort' => $data['sort'] ?? 0,
                'remark' => $data['remark'] ?? null,
                'created_by' => $data['created_by'] ?? null,
            ]);

            // Expand BOM materials into order materials
            if (!empty($bom)) {
                $orderQty = (float)$data['quantity'];
                foreach ($bom->materials as $bm) {
                    ProductionOrderMaterial::create([
                        'order_id' => $order->id,
                        'material_id' => $bm->material_id,
                        'required_quantity' => $bm->quantity * $orderQty,
                        'issued_quantity' => 0,
                        'remark' => $bm->remark,
                    ]);
                }
            }

            // Add direct materials if provided
            if (!empty($data['materials'])) {
                foreach ($data['materials'] as $m) {
                    ProductionOrderMaterial::create([
                        'order_id' => $order->id,
                        'material_id' => $m['material_id'],
                        'required_quantity' => $m['required_quantity'],
                        'issued_quantity' => 0,
                        'remark' => $m['remark'] ?? null,
                    ]);
                }
            }
        });
    }

    public static function update(array $data): void
    {
        $order = ProductionOrder::find($data['id']);
        if (!$order) {
            throw new BusinessException('工单不存在', ResponseCode::ERROR_PARAM->value);
        }
        if ($order->status !== 1) {
            throw new BusinessException('只能编辑待生产工单', ResponseCode::ERROR_PARAM->value);
        }

        $updateData = [];
        if (isset($data['bom_id'])) $updateData['bom_id'] = $data['bom_id'];
        if (isset($data['material_id'])) $updateData['material_id'] = $data['material_id'];
        if (isset($data['quantity'])) $updateData['quantity'] = $data['quantity'];
        if (isset($data['priority'])) $updateData['priority'] = $data['priority'];
        if (isset($data['plan_start_date'])) $updateData['plan_start_date'] = $data['plan_start_date'];
        if (isset($data['plan_end_date'])) $updateData['plan_end_date'] = $data['plan_end_date'];
        if (isset($data['sort'])) $updateData['sort'] = $data['sort'];
        if (isset($data['remark'])) $updateData['remark'] = $data['remark'];

        if (!empty($updateData)) {
            $order->fill($updateData);
            $order->save();
        }

        // Update materials if provided
        if (isset($data['materials'])) {
            ProductionOrderMaterial::where('order_id', $order->id)->delete();
            foreach ($data['materials'] as $m) {
                ProductionOrderMaterial::create([
                    'order_id' => $order->id,
                    'material_id' => $m['material_id'],
                    'required_quantity' => $m['required_quantity'],
                    'issued_quantity' => 0,
                    'remark' => $m['remark'] ?? null,
                ]);
            }
        }
    }

    public static function delete(int $id): void
    {
        $order = ProductionOrder::find($id);
        if (!$order) {
            throw new BusinessException('工单不存在', ResponseCode::ERROR_PARAM->value);
        }
        if ($order->status !== 1 && $order->status !== 4) {
            throw new BusinessException('只能删除待生产或已关闭工单', ResponseCode::ERROR_PARAM->value);
        }
        $order->delete();
    }

    public static function status(array $data): void
    {
        $order = ProductionOrder::find($data['id']);
        if (!$order) {
            throw new BusinessException('工单不存在', ResponseCode::ERROR_PARAM->value);
        }

        $newStatus = (int)$data['status'];
        $current = $order->status;

        // Validate state transitions
        $valid = match ($current) {
            1 => $newStatus === 2,           // 待生产 → 生产中
            2 => $newStatus === 3,           // 生产中 → 已完成
            3 => $newStatus === 4,           // 已完成 → 已关闭
            default => false,
        };

        if (!$valid) {
            throw new BusinessException('状态流转不合法', ResponseCode::ERROR_PARAM->value);
        }

        $update = ['status' => $newStatus];

        if ($newStatus === 2) {
            $update['actual_start_date'] = date('Y-m-d H:i:s');
        }
        if ($newStatus === 3) {
            $update['actual_end_date'] = date('Y-m-d H:i:s');
            $update['produced_quantity'] = $data['produced_quantity'] ?? $order->quantity;
        }

        $order->fill($update);
        $order->save();
    }

    public static function getMaterialsByBom(int $bomId, float $quantity): array
    {
        $bom = Bom::with('materials.material:id,code,name,spec')->find($bomId);
        if (!$bom) {
            throw new BusinessException('BOM不存在', ResponseCode::ERROR_PARAM->value);
        }

        return $bom->materials->map(function ($m) use ($quantity) {
            return [
                'material_id' => $m->material_id,
                'material_code' => $m->material?->code ?? '',
                'material_name' => $m->material?->name ?? '',
                'material_spec' => $m->material?->spec ?? '',
                'required_quantity' => $m->quantity * $quantity,
                'remark' => $m->remark ?? '',
            ];
        })->toArray();
    }
}
