<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Branch extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public $table = 'branches';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'member_prefix',
        'invoice_prefix',
        'primary_color',
        'address',
        'online_account_id',
        'sales_manager_id',
        'fitness_manager_id',
        'partner_percentage',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function accounts() : HasMany
    {
        return $this->hasMany(Account::class, 'branch_id');
    }

    public function online_account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'online_account_id');
    }

    public function sales_manager() : BelongsTo
    {
        return $this->belongsTo(User::class,'sales_manager_id');
    }

    public function payments() : HasManyThrough
    {
        return $this->hasManyThrough(Payment::class,Account::class,'branch_id','account_id');
    }

    public function getRemainingAttribute()
    {
        return $this->sales_manager && $this->sales_manager->employee ? ($this->sales_manager->employee->target_amount - $this->payments_sum_amount) : $this->payments_sum;
    }

    public function fitness_manager() : BelongsTo
    {
        return $this->belongsTo(User::class,'fitness_manager_id');
    }

    public function invoices() : HasMany
    {
        return $this->hasMany(Invoice::class,'branch_id');
    }
    
    public function memberships() : HasManyThrough
    {
        return $this->hasManyThrough(Membership::class,Invoice::class,'branch_id','id');
    }

    public function transactions() : HasManyThrough
    {
        return $this->hasManyThrough(Transaction::class,Account::class,'branch_id','account_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
