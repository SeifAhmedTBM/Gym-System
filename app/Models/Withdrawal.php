<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Withdrawal extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public $table = 'withdrawals';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'amount',
        'notes',
        'account_id',
        'created_by_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
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
        return ModelScope::filter($data, 'App\\Models\\Withdrawal');
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($withdrawal) { 
            $withdrawal->transaction()->delete();
        });
    }
}
