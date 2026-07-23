<?php

namespace app\common\model;

class StockCheckTask extends Model
{
    protected $table = 'stock_check_task';

    protected $fillable = [
        'code', 'warehouse_id', 'status', 'remark', 'created_by',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function records()
    {
        return $this->hasMany(StockCheckRecord::class, 'task_id');
    }
}
