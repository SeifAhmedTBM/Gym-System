<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Refund extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public const STATUS = [
        'pending'   => 'Pending',
        'confirmed' => 'Confirmed',
        'rejected'  => 'Rejected',
        'approved'  => 'Approved',
    ];

    public const STATUS_COLOR = [
        'pending'   => 'badge badge-warning',
        'confirmed' => 'badge badge-success',
        'rejected'  => 'badge badge-danger',
        'approved'  => 'badge badge-primary',
    ];

    public $table = 'refunds';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'refund_reason_id',
        'invoice_id',
        'account_id',
        'amount',
        'status',
        'created_by_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function refund_reason()
    {
        return $this->belongsTo(RefundReason::class, 'refund_reason_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class,'transactionable');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\Refund');
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($refund) { 
             $refund->transaction()->delete();
        });
    }
}
