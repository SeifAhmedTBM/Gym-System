<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
                'from_account',
                'to_account',
                'amount',
                'created_by_id',
            ];

    public function transaction()
    {
        return $this->morphOne(Transaction::class,'transactionable');
    }

    public function fromAccount()
    {
        return $this->belongsTo(Account::class,'from_account');
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class,'to_account');
    }
}
