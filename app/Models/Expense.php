<?php

namespace App\Models;

use Carbon\Carbon;
use \DateTimeInterface;
use App\Traits\Auditable;
use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public $table = 'expenses';

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'date',
        'amount',
        'note',
        'loan_id',
        'expenses_category_id',
        'created_by_id',
        'account_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function expenses_category()
    {
        return $this->belongsTo(ExpensesCategory::class, 'expenses_category_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class,'account_id');
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
        return ModelScope::filter($data, 'App\\Models\\Expense');
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($expense) { 
             $expense->transaction()->delete();
        });
    }
}
