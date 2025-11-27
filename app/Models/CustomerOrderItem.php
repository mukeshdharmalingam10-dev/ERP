<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_order_id',
        'tender_item_id',
        'po_sr_no',
        'ordered_qty',
    ];

    protected $casts = [
        'ordered_qty' => 'decimal:2',
    ];

    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    public function tenderItem()
    {
        return $this->belongsTo(TenderItem::class);
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


