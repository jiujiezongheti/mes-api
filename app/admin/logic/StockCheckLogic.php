<?php

namespace app\admin\logic;

use app\common\model\StockCheckTask;
use app\common\model\StockCheckRecord;
use app\common\model\Inventory;
use app\common\model\StockRecord;
use app\common\model\Material;
use app\common\exceptions\BusinessException;
use app\common\ResponseCode;
use Illuminate\Database\Capsule\Manager as DB;

class StockCheckLogic
{
    public static function getTaskList(array $params): array
    {
        $query = StockCheckTask::with('warehouse:id,code,name')->withCount('records');

        if (!empty($params['warehouse_id'])) {
            $query->where('warehouse_id', (int)$params['warehouse_id']);
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', (int)$params['status']);
        }
        if (!empty($params['created_by'])) {
            $query->where('created_by', (int)$params['created_by']);
        }

        $page = (int)($params['page'] ?? 1);
        $pageSize = (int)($params['pageSize'] ?? 50);
        $total = $query->count();
        $list = $query->orderBy('id', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        return ['list' => $list, 'total' => $total, 'page' => $page, 'pageSize' => $pageSize];
    }

    public static function createTask(array $data): void
    {
        $warehouseId = (int)($data['warehouse_id'] ?? 0);
        if (!$warehouseId) {
            throw new BusinessException('请选择仓库', ResponseCode::ERROR_PARAM->value);
        }

        $date = date('Ymd');
        $count = StockCheckTask::where('code', 'like', "QC-{$date}-%")->count();
        $code = 'QC-' . $date . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        StockCheckTask::create([
            'code' => $code,
            'warehouse_id' => $warehouseId,
            'status' => 0,
            'remark' => $data['remark'] ?? '',
            'created_by' => $data['created_by'] ?? null,
        ]);
    }

    public static function getTaskDetail(int $id): array
    {
        $task = StockCheckTask::with('warehouse:id,code,name')->find($id);
        if (!$task) {
            throw new BusinessException('任务不存在', ResponseCode::ERROR_PARAM->value);
        }

        $records = StockCheckRecord::with('material:id,code,name,spec')
            ->where('task_id', $id)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'material_id' => $item->material_id,
                    'material_code' => $item->material?->code ?? '',
                    'material_name' => $item->material?->name ?? '',
                    'material_spec' => $item->material?->spec ?? '',
                    'batch_no' => $item->batch_no ?? '',
                    'actual_quantity' => (float)$item->actual_quantity,
                    'remark' => $item->remark ?? '',
                    'created_at' => $item->created_at,
                ];
            });

        return [
            'task' => [
                'id' => $task->id,
                'code' => $task->code,
                'warehouse_id' => $task->warehouse_id,
                'warehouse_name' => $task->warehouse?->name ?? '',
                'status' => $task->status,
                'remark' => $task->remark ?? '',
                'created_at' => $task->created_at,
            ],
            'records' => $records,
            'record_count' => $records->count(),
        ];
    }

    public static function createRecord(array $data): void
    {
        $taskId = (int)($data['task_id'] ?? 0);
        $materialId = (int)($data['material_id'] ?? 0);
        $actualQty = (float)($data['actual_quantity'] ?? 0);

        if (!$taskId || !$materialId || $actualQty <= 0) {
            throw new BusinessException('参数错误', ResponseCode::ERROR_PARAM->value);
        }

        $task = StockCheckTask::find($taskId);
        if (!$task) {
            throw new BusinessException('任务不存在', ResponseCode::ERROR_PARAM->value);
        }
        if ($task->status !== 0) {
            throw new BusinessException('任务已结束，不可继续录入', ResponseCode::ERROR_PARAM->value);
        }

        $material = Material::find($materialId);
        if (!$material) {
            throw new BusinessException('物料不存在', ResponseCode::ERROR_PARAM->value);
        }

        StockCheckRecord::create([
            'task_id' => $taskId,
            'material_id' => $materialId,
            'batch_no' => $data['batch_no'] ?? '',
            'actual_quantity' => $actualQty,
            'remark' => $data['remark'] ?? '',
            'created_by' => $data['created_by'] ?? null,
        ]);
    }

    public static function completeTask(int $id, int $userId): void
    {
        $task = StockCheckTask::find($id);
        if (!$task) {
            throw new BusinessException('任务不存在', ResponseCode::ERROR_PARAM->value);
        }
        if ($task->status !== 0) {
            throw new BusinessException('任务状态不正确', ResponseCode::ERROR_PARAM->value);
        }

        $task->status = 1;
        $task->save();
    }

    public static function approveTask(int $id, int $userId): void
    {
        $task = StockCheckTask::with('records')->find($id);
        if (!$task) {
            throw new BusinessException('任务不存在', ResponseCode::ERROR_PARAM->value);
        }
        if ($task->status !== 1) {
            throw new BusinessException('只能审核已完成的任务', ResponseCode::ERROR_PARAM->value);
        }

        DB::transaction(function () use ($task, $userId) {
            // 1. 按物料汇总实盘数量（同物料多次扫码累加）
            $totals = [];
            foreach ($task->records as $record) {
                $mid = $record->material_id;
                $totals[$mid] = ($totals[$mid] ?? 0) + (float)$record->actual_quantity;
            }

            // 2. 预取当前库存快照
            $materialIds = array_keys($totals);
            $inventories = Inventory::where('warehouse_id', $task->warehouse_id)
                ->whereIn('material_id', $materialIds)
                ->get()
                ->keyBy('material_id');

            // 3. 逐物料更新库存 + 生成流水
            foreach ($totals as $materialId => $after) {
                $inv = $inventories->get($materialId);
                $before = $inv ? (float)$inv->quantity : 0;
                $diff = $after - $before;

                if ($diff !== 0.0) {
                    if (!$inv && $after > 0) {
                        $inv = new Inventory();
                        $inv->warehouse_id = $task->warehouse_id;
                        $inv->material_id = $materialId;
                        $inv->quantity = $after;
                        $inv->locked_quantity = 0;
                        $inv->save();
                    } elseif ($inv) {
                        $inv->quantity = $after;
                        $inv->save();
                    }

                    StockRecord::create([
                        'warehouse_id' => $task->warehouse_id,
                        'material_id' => $materialId,
                        'type' => $diff > 0 ? 3 : 4,
                        'quantity' => $diff,
                        'before_quantity' => $before,
                        'after_quantity' => $after,
                        'source_type' => 'check_task',
                        'source_id' => $task->id,
                        'remark' => '盘点审核',
                        'created_by' => $userId,
                    ]);
                }
            }

            $task->status = 2;
            $task->save();
        });
    }

    public static function rejectTask(int $id, int $userId): void
    {
        $task = StockCheckTask::find($id);
        if (!$task) {
            throw new BusinessException('任务不存在', ResponseCode::ERROR_PARAM->value);
        }
        if ($task->status !== 1) {
            throw new BusinessException('只能驳回已完成的任务', ResponseCode::ERROR_PARAM->value);
        }

        $task->status = 3;
        $task->save();
    }
}
