<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ExternalPayment extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public $table = 'external_payments';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title',
        'lead_id',
        'amount',
        'notes',
        'account_id',
        'created_by_id',
        'external_payment_category_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function external_payment_category() : BelongsTo
    {
        return $this->belongsTo(ExternalPaymentCategory::class,'external_payment_category_id');
    }
    
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function transaction() : MorphOne
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\ExternalPayment');
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($externalPayment) { 
            $externalPayment->transaction()->delete();
        });
    }

    public function lead() : BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}
