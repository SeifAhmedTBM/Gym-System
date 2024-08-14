<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesTiersRange extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function sales_tier() : BelongsTo
    {
        return $this->belongsTo(SalesTier::class, 'sales_tier_id', 'id');
    }
}
