<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderTechnicalSpecification extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_item_id',
        'specification',
        'rank',
    ];

    public function tenderItem()
    {
        return $this->belongsTo(TenderItem::class);
    }
}
