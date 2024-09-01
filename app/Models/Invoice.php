<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    public const IS_REVIEWED_COLORS = [
        false           => 'badge badge-warning',
        true            => 'badge badge-primary'
    ];

    public const IS_REVIEWED_TRANS = [
        false           => 'not_reviewed',
        true            => 'is_reviewed'
    ];

    public const STATUS_SELECT = [
        'fullpayment'   => 'Full Payment',
        'partial'       => 'Partial',
        'refund'        => 'Refund',
        'settlement'    => 'Settlement',
    ];

    public const REVIEW_SELECT = [
        false           => 'Not reviewed',
        true            => 'reviewed',
    ];

    public const STATUS_COLOR = [
        'fullpayment'   => 'success',
        'partial'       => 'danger',
        'refund'        => 'danger',
        'settlement'    => 'info',
    ];

    public $table = 'invoices';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $with = ['payments'];

    // protected $guarded = ['id'];
    protected $guarded = [];  

    public function membership()
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function member()
    {
        return $this->hasOneThrough(Membership::class,Invoice::class, 'membership_id' ,'member_id');
    }

    public function refund()
    {
        return $this->hasOne(Refund::class,'invoice_id');
    }

    public function sales_by()
    {
        return $this->belongsTo(User::class, 'sales_by_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function invoicePrefix()
    {
        return Setting::first()->invoice_prefix ?? '';
    }

    public function payments() :HasMany
    {
        return $this->hasMany(Payment::class,'invoice_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\Invoice');
    }

    public function getRestAttribute()
    {
        return $this->net_amount - $this->payments->sum('amount');
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($invoice) 
        { 
            if ($invoice->membership && $invoice->membership->member && $invoice->membership->member->memberships) 
            {
                if($invoice->membership->member->memberships()->count() <= 1){
                    $invoice->membership->member->type = 'lead';
                    $invoice->membership->member->member_code = NULL;
                    $invoice->membership->member->save();

                    // $invoice->membership->member->user->delete();
                }
            }

            $invoice->membership->reminders()->delete();
            $invoice->membership->histories()->delete();
            
            $invoice->membership()->delete();
            foreach($invoice->payments as $payment){
                $payment->transaction->delete();
            }
            $invoice->payments()->delete();
        });
    }

    public function trainer() : BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id', 'id');
    }
    public function services()
    {
        return $this->belongsTo(Service::class);
    }
}
