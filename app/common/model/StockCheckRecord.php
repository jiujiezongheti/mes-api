<?php

namespace app\common\model;

class StockCheckRecord extends Model
{
    protected $table = 'stock_check_record';

    protected $fillable = [
        'task_id', 'material_id', 'batch_no', 'actual_quantity', 'remark', 'created_by',
    ];

    protected $casts = [
        'actual_quantity' => 'float',
    ];

    public function task()
    {
        return $this->belongsTo(StockCheckTask::class, 'task_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
