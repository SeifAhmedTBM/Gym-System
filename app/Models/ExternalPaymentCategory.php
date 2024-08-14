<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExternalPaymentCategory extends Model
{
    use HasFactory;

    public $table = 'external_payment_categories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function external_payments() : HasMany
    {
        return $this->hasMany(ExternalPayment::class,'external_payment_category_id');
    }
}
