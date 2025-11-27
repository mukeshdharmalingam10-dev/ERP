<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'order_date',
        'tender_id',
        'branch_id',
    ];

    protected $casts = [
        'order_date' => 'date',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    public function schedules()
    {
        return $this->hasMany(CustomerOrderSchedule::class);
    }

    public function amendments()
    {
        return $this->hasMany(CustomerOrderAmendment::class);
    }
}


