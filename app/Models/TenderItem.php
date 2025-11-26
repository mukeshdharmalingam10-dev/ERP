<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'pl_code',
        'title',
        'description',
        'delivery_location',
        'qty',
        'unit_id',
        'request_for_price',
        'price_received',
        'price_quoted',
        'tender_status',
        'bid_result',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'price_received' => 'decimal:2',
        'price_quoted' => 'decimal:2',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function technicalSpecifications()
    {
        return $this->hasMany(TenderTechnicalSpecification::class);
    }
}
