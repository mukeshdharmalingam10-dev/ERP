<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyProductionListEntry extends Model
{
    use HasFactory;

    public $timestamps = false;
    const UPDATED_AT = null;

    protected $fillable = [
        'dpl_id',
        'set_no',
        'sub_set_no',
        'item_name',
        'qty',
        'completed_qty',
        'entry_date',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'completed_qty' => 'decimal:2',
        'entry_date' => 'date',
    ];

    public function dpl()
    {
        return $this->belongsTo(DailyProductionList::class, 'dpl_id');
    }
}

