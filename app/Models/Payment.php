<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Payment extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public $table = 'payments';

    protected $casts = [
        'amount'        => 'float'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'payment_method',
        'amount',
        'invoice_id',
        'account_id',
        'sales_by_id',
        'created_by_id',
        'notes',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function sales_by()
    {
        return $this->belongsTo(User::class, 'sales_by_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class,'transactionable');
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($payment) { 
            $payment->transaction()->delete();
        });
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\Payment');
    }

}
