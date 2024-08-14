<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public $table = 'accounts';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'opening_balance',
        'balance',
        'manager',
        'branch_id',
        'commission_percentage',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class,'account_id');
    }
    
    public function branch() : BelongsTo
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
}
