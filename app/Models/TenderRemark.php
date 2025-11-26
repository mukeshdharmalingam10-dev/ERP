<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderRemark extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'date',
        'remarks',
        'corrigendum_file',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }
}
