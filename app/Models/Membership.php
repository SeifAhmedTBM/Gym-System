<?php

namespace App\Models;

use Carbon\Carbon;
use \DateTimeInterface;
use App\Models\Setting;
use App\Traits\Auditable;
use App\Http\Helpers\ModelScope;
use App\Models\LeadRemindersHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Membership extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public $table = 'memberships';


    public const STATUS = [
        'expired'       => 'danger',
        'pending'       => 'primary',
        'expiring'      => 'warning',
        'current'       => 'success',
        'refunded'      => 'danger'
    ];

    public const SELECT_STATUS = [
        'expired'       => 'Expired',
        'pending'       => 'Pending',
        'expiring'      => 'Expiring',
        'current'       => 'Current',
        'refunded'      => 'Refunded'
    ];

    public const MEMBERSHIP_STATUS = [
        'new'           => 'New',
        'renew'         => 'Renew'
    ];

    public const MEMBERSHIP_STATUS_COLOR = [
        'new'           => 'primary',
        'renew'         => 'success'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'start_date',
        'end_date',
        'member_id',
        'trainer_id',
        'status',
        'service_pricelist_id',
        'is_changed',
        'sales_by_id',
        'last_attendance',
        'notes',
        'membership_status',
        'sport_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'assigned_coach_id',
        'assign_date',
    ];

    public function assigned_coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_coach_id');
    }

    public function getStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function member()
    {
        return $this->belongsTo(Lead::class, 'member_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
    

    public function service_pricelist()
    {
        return $this->belongsTo(Pricelist::class, 'service_pricelist_id')->withTrashed();
    }

    public function sales_by()
    {
        return $this->belongsTo(User::class, 'sales_by_id');
    }

    public function attendances()
    {
        return $this->hasMany(MembershipAttendance::class, 'membership_id');
    }

    public function freezeRequests()
    {
        return $this->hasMany(FreezeRequest::class, 'membership_id');
    }

    public function memberPrefix()
    {
        return Setting::first()->member_prefix ?? '';
    }

    public function trackMembership()
    {
        return $this->hasMany(TrackMembership::class, 'membership_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'membership_id');
    }

    public function remained()
    {
        return $this->service_pricelist->freeze_count - $this->freezeRequests()->whereStatus('confirmed')->count();
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Invoice::class, 'membership_id', 'invoice_id');
    }

    public function histories()
    {
        return $this->hasMany(LeadRemindersHistory::class, 'membership_id');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'membership_id');
    }

    public function free_sessions()
    {
        return $this->hasMany(FreeSessions::class, 'membership_id');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'membership_id');
    }

    public function membership_schedules(): HasMany
    {
        return $this->hasMany(MembershipSchedule::class, 'membership_id');
    }

    public function trainer_attendances(): HasMany
    {
        return $this->hasMany(TrainerAttendant::class, 'membership_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\Membership');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($membership) {
            if ($membership->payments()) {
                foreach ($membership->payments as $key => $payment) {
                    if ($payment->transaction) {
                        $payment->transaction->delete();
                    }
                    $payment->delete();
                }
            }

            if ($membership->reminders()) {
                $membership->reminders()->delete();
            }

            if ($membership->attendances()) {
                $membership->attendances()->delete();
            }

            if ($membership->freezeRequests()) {
                $membership->freezeRequests()->delete();
            }

            if ($membership->trackMembership()) {
                $membership->trackMembership()->delete();
            }
        });
    }

    public function membership_service_options()
    {
        return $this->hasMany(MembershipServiceOptions::class, 'membership_id');
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class, 'sport_id', 'id');
    }

   
}
