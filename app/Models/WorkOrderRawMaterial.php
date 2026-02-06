<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderRawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'raw_material_id',
        'work_order_quantity',
        'unit_id',
        'sr_no',
    ];

    protected $casts = [
        'work_order_quantity' => 'decimal:2',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
