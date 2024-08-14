<?php

namespace App\Models;

use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    public $table = 'transactions';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'transactionable_type',
        'transactionable_id',
        'account_id',
        'amount',
        'created_by',
        'created_at',
        'updated_at'
    ];

    const SELECT = [
        'App\\Models\\Expense'            => 'Expenses',
        'App\\Models\\Refund'             => 'Refunds',
        'App\\Models\\ExternalPayment'    => 'External Payments',
        'App\\Models\\Payment'            => 'Payments',
        'App\\Models\\Withdrawal'         => 'Withdrawal',
        'App\\Models\\Transfer'           => 'Transfer',
        'App\\Models\\Loan'               => 'Loan',
    ];

    const type = [
        'App\Models\Expense'            => 'Expenses',
        'App\Models\Refund'             => 'Refunds',
        'App\Models\ExternalPayment'    => 'External Payments',
        'App\Models\Payment'            => 'Payments',
        'App\Models\Withdrawal'         => 'Withdrawal',
        'App\Models\Transfer'           => 'Transfer',
        'App\Models\Loan'               => 'Loan',
    ];

    const color = [
        'App\Models\Expense'            => 'bg-danger',
        'App\Models\Refund'             => 'bg-danger',
        'App\Models\ExternalPayment'    => 'bg-info',
        'App\Models\Payment'            => 'bg-info',
        'App\Models\Withdrawal'         => 'bg-danger',
        'App\Models\Transfer'           => 'bg-success',
        'App\Models\Loan'               => 'bg-danger',
    ];

    public function transactionable() : MorphTo
    {
        return $this->morphTo();
    }
    
    public function account()
    {
        return $this->belongsTo(Account::class,'account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\Transaction');
    }
}
