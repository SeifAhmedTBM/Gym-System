<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loan extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public $table = 'loans';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'employee_id',
        'name',
        'description',
        'amount',
        'account_id',
        'created_by_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class,'transactionable');
    }

    public function account()
    {
        return $this->belongsTo(Account::class,'account_id');
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($loan) { 
            $loan->transaction()->delete();
        });
    }

    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\Loan');
    }
}
