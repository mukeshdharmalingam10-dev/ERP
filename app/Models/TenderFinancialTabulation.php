<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderFinancialTabulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'pl_number',
        'bid_closed_date',
    ];

    protected $casts = [
        'bid_closed_date' => 'date',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }
}
