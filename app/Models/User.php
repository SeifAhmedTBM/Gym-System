<?php

namespace App\Models;

use Hash;
use Carbon\Carbon;
use \DateTimeInterface;
use App\Http\Helpers\ModelScope;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable
{
    use HasApiTokens;
    use SoftDeletes;
    use Notifiable;
    use HasFactory;

    public $table = 'users';

    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'order',
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'phone',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('id', 1)->exists();
    }

    public function userUserAlerts()
    {
        return $this->belongsToMany(UserAlert::class);
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? bcrypt($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function logs()
    {
        return $this->hasMany(AuditLog::class,'user_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function memberships() : HasMany
    {
        return $this->hasMany(Membership::class, 'sales_by_id', 'id');
    }

    public function trackMemberships()
    {
        return $this->hasManyThrough(TrackMembership::class,Membership::class,'sales_by_id','membership_id');
    }

    public function sales_tier() : HasOne
    {
        return $this->hasOne(SalesTiersUser::class, 'user_id', 'id');
    }

    public function trainer_memberships() : HasMany
    {
        return $this->hasMany(Membership::class, 'trainer_id', 'id');
    }

    public function previous_trainer_memberships() : HasMany
    {
        return $this->hasMany(Membership::class, 'trainer_id', 'id');
    }

    public function trainer_invoices() : HasManyThrough
    {
        return $this->hasManyThrough(Invoice::class,Membership::class, 'trainer_id', 'membership_id');
    }

    public function sessions() : HasMany
    {
        return $this->hasMany(TrainerAttendant::class, 'trainer_id', 'id');
    }

    public function ratings() :HasMany
    {
        return $this->hasMany(Rating::class,'trainer_id');
    }

    public function countSessions()
    {
        $sessions_count = 0;

        $sessions = $this->sessions()->get()->groupBy(['schedule_id', function($item) {
            return $item->created_at->format('Y-m-d');
        }]);
        
        foreach($sessions as $key => $session) {
            $sessions_count += count($session);
        }
        
        return $sessions_count;
    }
    
    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\User');
    }

    public function invoice_refunds() : HasManyThrough
    {
        return $this->hasManyThrough(Refund::class, Invoice::class, 'sale_by_id', 'invoice_id');
    }

    public function lead() : HasOne
    {
        return $this->hasOne(Lead::class);
    }

    public function employee() : HasOne
    {
        return $this->hasOne(Employee::class, 'user_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class,'sales_by_id');
    }

    public function invoices()
    {
        // return $this->hasManyThrough(Invoice::class, Payment::class, 'invoice_id', 'sales_by_id');
        return $this->hasMany(Invoice::class,'sales_by_id');
    }

    public function schedules() : HasMany
    {
        return $this->hasMany(Schedule::class, 'trainer_id', 'id');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class,'user_id');
    }

    public function reminders_histories()
    {
        return $this->hasMany(LeadRemindersHistory::class,'user_id');
    }

    public function overdueReminders()
    {
        return $this->hasMany(Reminder::class,'user_id')->whereDate('due_date','<',date('Y-m-d'));
    }
    public function todayReminders()
    {
        return $this->hasMany(Reminder::class,'user_id')->whereDate('due_date',date('Y-m-d'));
    }
    public function upcommingReminders()
    {
        return $this->hasMany(Reminder::class,'user_id')
                    ->whereDate('due_date','>',date('Y-m-d'))
                    ->whereBetween('due_date',[date('Y-m-01'),date('Y-m-d',strtotime(date('Y-m-t').'+1 Day'))]);
    }
}
