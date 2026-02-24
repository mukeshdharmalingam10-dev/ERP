<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyProductionList extends Model
{
    use HasFactory;

    protected $fillable = [
        'dpl_no',
        'production_order_id',
        'work_order_id',
        'remarks',
        'latest_date',
        'created_by',
    ];

    protected $casts = [
        'latest_date' => 'date',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function entries()
    {
        return $this->hasMany(DailyProductionListEntry::class, 'dpl_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function nextDplNo(): string
    {
        $last = static::query()->orderByDesc('id')->value('dpl_no');
        $seq = 1;
        if ($last && preg_match('/DPL-(\d+)$/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }
        return sprintf('DPL-%03d', $seq);
    }
}

