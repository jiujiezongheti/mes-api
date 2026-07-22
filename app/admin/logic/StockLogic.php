<?php

namespace app\admin\logic;

use app\common\model\Inventory;
use app\common\model\InventoryCheck;
use app\common\model\InventoryCheckItem;
use app\common\model\Material;
use app\common\model\StockRecord;
use app\common\model\Warehouse;
use app\common\exceptions\BusinessException;
use app\common\ResponseCode;
use Illuminate\Database\Capsule\Manager as DB;

class StockLogic
{
    // ===== 库存台账 =====

    public static function getInventoryList(array $params): array
    {
        $query = Inventory::with(['warehouse:id,code,name', 'material' => function ($q) {
            $q->select('id', 'code', 'name', 'spec', 'unit_id');
        }]);

        if (!empty($params['warehouse_id'])) {
            $query->where('warehouse_id', (int)$params['warehouse_id']);
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

        $page = (int)($params['page'] ?? 1);
        $pageSize = (int)($params['pageSize'] ?? 20);
        $total = $query->count();
        $list = $query->orderBy('id', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'warehouse_id' => $item->warehouse_id,
                    'warehouse_code' => $item->warehouse?->code ?? '',
                    'warehouse_name' => $item->warehouse?->name ?? '',
                    'material_id' => $item->material_id,
                    'material_code' => $item->material?->code ?? '',
                    'material_name' => $item->material?->name ?? '',
                    'material_spec' => $item->material?->spec ?? '',
                    'quantity' => (float)$item->quantity,
                    'locked_quantity' => (float)$item->locked_quantity,
                    'available_quantity' => (float)$item->quantity - (float)$item->locked_quantity,
                ];
            });

        return ['list' => $list, 'total' => $total, 'page' => $page, 'pageSize' => $pageSize];
    }

    // ===== 入库 =====

    public static function in(array $data): void
    {
        $qty = (float)$data['quantity'];
        DB::transaction(function () use ($data, $qty) {
            $inv = self::getOrCreateInventory($data['warehouse_id'], $data['material_id']);
            $before = (float)$inv->quantity;

            $inv->quantity = $before + $qty;
            $inv->save();

            StockRecord::create([
                'warehouse_id' => $data['warehouse_id'],
                'material_id' => $data['material_id'],
                'type' => 1,
                'quantity' => $qty,
                'before_quantity' => $before,
                'after_quantity' => (float)$inv->quantity,
                'source_type' => $data['source_type'] ?? 'manual',
                'source_id' => $data['source_id'] ?? null,
                'remark' => $data['remark'] ?? null,
                'created_by' => $data['created_by'] ?? null,
            ]);
        });
    }

    // ===== 出库 =====

    public static function out(array $data): void
    {
        $qty = (float)$data['quantity'];
        DB::transaction(function () use ($data, $qty) {
            $inv = self::getOrCreateInventory($data['warehouse_id'], $data['material_id']);
            $before = (float)$inv->quantity;
            $available = $before - (float)$inv->locked_quantity;

            if ($qty > $available) {
                throw new BusinessException('库存不足', ResponseCode::ERROR_PARAM->value);
            }

            $inv->quantity = $before - $qty;
            $inv->save();

            StockRecord::create([
                'warehouse_id' => $data['warehouse_id'],
                'material_id' => $data['material_id'],
                'type' => 2,
                'quantity' => -$qty,
                'before_quantity' => $before,
                'after_quantity' => (float)$inv->quantity,
                'source_type' => $data['source_type'] ?? 'manual',
                'source_id' => $data['source_id'] ?? null,
                'remark' => $data['remark'] ?? null,
                'created_by' => $data['created_by'] ?? null,
            ]);
        });
    }

    // ===== 锁定/解锁库存（为工单预留）=====

    public static function lockStock(int $warehouseId, int $materialId, float $quantity): void
    {
        $inv = self::getOrCreateInventory($warehouseId, $materialId);
        $available = (float)$inv->quantity - (float)$inv->locked_quantity;
        if ($quantity > $available) {
            throw new BusinessException('库存不足，无法锁定', ResponseCode::ERROR_PARAM->value);
        }
        $inv->locked_quantity = (float)$inv->locked_quantity + $quantity;
        $inv->save();
    }

    public static function unlockStock(int $warehouseId, int $materialId, float $quantity): void
    {
        $inv = self::getOrCreateInventory($warehouseId, $materialId);
        $newLocked = max(0, (float)$inv->locked_quantity - $quantity);
        $inv->locked_quantity = $newLocked;
        $inv->save();
    }

    // ===== 库存流水 =====

    public static function getRecordList(array $params): array
    {
        $query = StockRecord::with(['warehouse:id,code,name', 'material' => function ($q) {
            $q->select('id', 'code', 'name', 'spec');
        }]);

        if (!empty($params['warehouse_id'])) {
            $query->where('warehouse_id', (int)$params['warehouse_id']);
        }
        if (!empty($params['material_id'])) {
            $query->where('material_id', (int)$params['material_id']);
        }
        if (!empty($params['material_code'])) {
            $query->whereHas('material', function ($q) use ($params) {
                $q->where('code', 'like', "%{$params['material_code']}%");
            });
        }
        if (isset($params['type']) && $params['type'] !== '') {
            $query->where('type', (int)$params['type']);
        }
        if (!empty($params['start_date'])) {
            $query->where('created_at', '>=', $params['start_date']);
        }
        if (!empty($params['end_date'])) {
            $query->where('created_at', '<=', $params['end_date'] . ' 23:59:59');
        }
        if (!empty($params['source_type'])) {
            $query->where('source_type', $params['source_type']);
        }

        $page = (int)($params['page'] ?? 1);
        $pageSize = (int)($params['pageSize'] ?? 20);
        $total = $query->count();
        $list = $query->orderBy('id', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'warehouse_id' => $item->warehouse_id,
                    'warehouse_name' => $item->warehouse?->name ?? '',
                    'material_id' => $item->material_id,
                    'material_code' => $item->material?->code ?? '',
                    'material_name' => $item->material?->name ?? '',
                    'type' => $item->type,
                    'quantity' => (float)$item->quantity,
                    'before_quantity' => (float)$item->before_quantity,
                    'after_quantity' => (float)$item->after_quantity,
                    'source_type' => $item->source_type,
                    'source_id' => $item->source_id,
                    'remark' => $item->remark ?? '',
                    'created_by' => $item->created_by,
                    'created_at' => $item->created_at,
                ];
            });

        return ['list' => $list, 'total' => $total, 'page' => $page, 'pageSize' => $pageSize];
    }

    // ===== 盘点 =====

    public static function checkCreate(array $data): void
    {
        DB::transaction(function () use ($data) {
            $code = 'PD-' . date('Ymd') . '-' . str_pad(InventoryCheck::count() + 1, 4, '0', STR_PAD_LEFT);
            $check = InventoryCheck::create([
                'code' => $code,
                'warehouse_id' => $data['warehouse_id'],
                'status' => 1,
                'remark' => $data['remark'] ?? null,
                'created_by' => $data['created_by'] ?? null,
            ]);

            $materials = Inventory::where('warehouse_id', $data['warehouse_id'])->get();
            foreach ($materials as $inv) {
                InventoryCheckItem::create([
                    'check_id' => $check->id,
                    'material_id' => $inv->material_id,
                    'book_quantity' => $inv->quantity,
                    'actual_quantity' => $inv->quantity,
                ]);
            }
        });
    }

    public static function checkGetItems(int $id): array
    {
        $check = InventoryCheck::with('warehouse:id,code,name')->find($id);
        if (!$check) {
            throw new BusinessException('盘点单不存在', ResponseCode::ERROR_PARAM->value);
        }
        $items = InventoryCheckItem::with('material:id,code,name,spec,unit_id')
            ->where('check_id', $id)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'material_id' => $item->material_id,
                    'material_code' => $item->material?->code ?? '',
                    'material_name' => $item->material?->name ?? '',
                    'material_spec' => $item->material?->spec ?? '',
                    'book_quantity' => (float)$item->book_quantity,
                    'actual_quantity' => (float)$item->actual_quantity,
                    'difference' => (float)$item->actual_quantity - (float)$item->book_quantity,
                    'remark' => $item->remark ?? '',
                ];
            });

        return [
            'check' => [
                'id' => $check->id,
                'code' => $check->code,
                'warehouse_id' => $check->warehouse_id,
                'warehouse_name' => $check->warehouse?->name ?? '',
                'status' => $check->status,
                'remark' => $check->remark ?? '',
            ],
            'items' => $items,
        ];
    }

    public static function checkComplete(array $data): void
    {
        $check = InventoryCheck::find($data['id']);
        if (!$check) {
            throw new BusinessException('盘点单不存在', ResponseCode::ERROR_PARAM->value);
        }
        if ($check->status === 2) {
            throw new BusinessException('盘点单已完成', ResponseCode::ERROR_PARAM->value);
        }

        DB::transaction(function () use ($check, $data) {
            foreach ($data['items'] as $item) {
                $checkItem = InventoryCheckItem::where('check_id', $check->id)
                    ->where('material_id', $item['material_id'])
                    ->first();

                $actual = (float)($item['actual_quantity'] ?? 0);
                $book = $checkItem ? (float)$checkItem->book_quantity : 0;
                $diff = $actual - $book;

                if ($checkItem) {
                    $checkItem->actual_quantity = $actual;
                    $checkItem->save();
                }

                $inv = Inventory::where('warehouse_id', $check->warehouse_id)
                    ->where('material_id', $item['material_id'])
                    ->first();

                if ($diff !== 0.0) {
                    $before = $inv ? (float)$inv->quantity : 0;
                    $after = $actual;

                    if (!$inv && $actual > 0) {
                        $inv = new \app\common\model\Inventory();
                        $inv->warehouse_id = $check->warehouse_id;
                        $inv->material_id = $item['material_id'];
                        $inv->quantity = $actual;
                        $inv->locked_quantity = 0;
                        $inv->save();
                    } elseif ($inv) {
                        $inv->quantity = $actual;
                        $inv->save();
                    }

                    $type = $diff > 0 ? 3 : 4;
                    StockRecord::create([
                        'warehouse_id' => $check->warehouse_id,
                        'material_id' => $item['material_id'],
                        'type' => $type,
                        'quantity' => $diff,
                        'before_quantity' => $before,
                        'after_quantity' => $after,
                        'source_type' => 'check',
                        'source_id' => $check->id,
                        'remark' => "盘点调整: {$item['remark']}",
                        'created_by' => $data['created_by'] ?? null,
                    ]);
                }
            }

            $check->status = 2;
            $check->save();
        });
    }

    public static function checkGetList(array $params): array
    {
        $query = InventoryCheck::with('warehouse:id,code,name');

        if (!empty($params['code'])) {
            $query->where('code', 'like', "%{$params['code']}%");
        }
        if (!empty($params['warehouse_id'])) {
            $query->where('warehouse_id', (int)$params['warehouse_id']);
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', (int)$params['status']);
        }

        $page = (int)($params['page'] ?? 1);
        $pageSize = (int)($params['pageSize'] ?? 20);
        $total = $query->count();
        $list = $query->orderBy('id', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        return ['list' => $list, 'total' => $total, 'page' => $page, 'pageSize' => $pageSize];
    }

    // ===== 工具方法 =====

    private static function getOrCreateInventory(int $warehouseId, int $materialId): Inventory
    {
        $inv = Inventory::where('warehouse_id', $warehouseId)
            ->where('material_id', $materialId)
            ->first();

        if (!$inv) {
            $inv = new Inventory();
            $inv->warehouse_id = $warehouseId;
            $inv->material_id = $materialId;
            $inv->quantity = 0;
            $inv->locked_quantity = 0;
            $inv->save();
        }

        return $inv;
    }
}
