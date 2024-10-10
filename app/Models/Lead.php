<?php

namespace App\Models;

use Carbon\Carbon;
use \DateTimeInterface;
use App\Models\Setting;
use App\Traits\Auditable;
use App\Models\Membership;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelScope;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Notifications\Notifiable;
class Lead extends Model implements HasMedia
{
    // use SoftDeletes;
    use InteractsWithMedia;
    use Auditable;
    use HasFactory;
    use HasApiTokens;
    use Notifiable;

    public const TYPE_SELECT = [
        'lead'   => 'Lead',
        'member' => 'Member',
    ];

    public const GENDER_SELECT = [
        'male'   => 'Male',
        'female' => 'Female',
    ];

    public $table = 'leads';

    protected $appends = [
        'photo',
        'profile_photo',
    ];

    protected $dates = [
        'dob',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'id',
        'name',
        'phone',
        'national',
        'member_code',
        'status_id',
        'source_id',
        'address_id',
        'card_number',
        'dob',
        'gender',
        'notes',
        'downloaded_app',
        'sales_by_id',
        'created_by_id',
        'type',
        'user_id',
        'referral_member',
        'whatsapp_number',
        'address_details',
        'parent_phone',
        'parent_phone_two',
        'sport_id',
        'parent_details',
        'branch_id',
        'invitation',
        'trainer_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'medical_background'
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function getProfilePhotoAttribute()
    {
        $file = $this->getMedia('photo')->last();
        if ($file) {

            return [
                "url"=> $file->getUrl(),
                "thumbnail"=> $file->getUrl('thumb'),
                "preview"=> $file->getUrl('preview'),
            ];
        }
        return [
            "url"=> "",
            "thumbnail"=>"",
            "preview"=>"",
        ];

    }
    public function getPhotoAttribute()
    {
        $file = $this->getMedia('photo')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }

        return $file;
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function getDobAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDobAttribute($value)
    {
        $this->attributes['dob'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function sales_by()
    {
        return $this->belongsTo(User::class, 'sales_by_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
    

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class, 'member_id');
    }

    public function memberPrefix()
    {
        return $this->branch->member_prefix ?? '';
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function trainer_attendants(): HasMany
    {
        return $this->hasMany(TrainerAttendant::class, 'member_id');
    }

    public function leadReminder()
    {
        return $this->hasOne(Reminder::class, 'lead_id');
    }

    public function leadReminders()
    {
        return $this->hasMany(Reminder::class, 'lead_id');
    }

    public function sales_reminders()
    {
        return $this->hasMany(Reminder::class, 'lead_id')->whereNotIn('type',['pt_session']);
    }

    public function trainer_reminders()
    {
        return $this->hasMany(Reminder::class, 'lead_id')->whereIn('type',['pt_session']);
    }

    public function reminderHistory()
    {
        return $this->hasMany(LeadRemindersHistory::class, 'lead_id');
    }

    public function sales_reminder_histories()
    {
        return $this->hasMany(LeadRemindersHistory::class, 'lead_id')->whereNotIn('type',['pt_session']);
    }

    public function trainer_reminder_histories()
    {
        return $this->hasMany(LeadRemindersHistory::class, 'lead_id')->whereIn('type',['pt_session']);
    }

    public function getCoachByAttribute()
    {
        $membership = $this->memberships()->whereNotNull('assigned_coach_id')->latest()->first();
        return $membership ? $membership->assigned_coach : '-';
        // return $this->memberships && $this->memberships->last() ? $this->memberships->last()->assigned_coach : '-';
    }

    public function getPtCoachByAttribute()
    {
        $membership = $this->memberships()->whereNotNull('trainer_id')->latest()->first();
        return $membership ? $membership->trainer : '-';
        // return $this->memberships && $this->memberships->last() ? $this->memberships->last()->assigned_coach : '-';
    }

    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\Lead');
    }

    public function getMyAttendanceCount($schedules)
    {
        return TrainerAttendant::where('member_id', $this->id)->whereIn('schedule_id', $schedules)->count();
    }

    public function  getLastMembershipAttribute()
    {
        // return Membership::whereMemberId($this->id)->orderBy('id','Desc')->first();
        return $this->memberships->last();
    }

    public function membership_attendances()
    {
        return $this->hasManyThrough(MembershipAttendance::class,Membership::class,'member_id','membership_id');
    }

    public function membership_schedules()
    {
        return $this->hasManyThrough(MembershipSchedule::class, Membership::class, 'member_id', 'membership_id');
    }

    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, Membership::class, 'member_id', 'membership_id');
    }

    public function messages()
    {
        return $this->hasMany(Sms::class, 'numbers', 'phone');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'member_id');
    }

    public function memberRequests()
    {
        return $this->hasMany(MemberRequest::class, 'member_id');
    }

    public function freezeRequests()
    {
        return $this->hasManyThrough(FreezeRequest::class, Membership::class, 'member_id', 'membership_id');
    }

    public function freeSessions()
    {
        return $this->hasManyThrough(FreeSessions::class, Membership::class, 'member_id', 'membership_id');
    }

    public function Notes()
    {
        return $this->hasMany(Note::class, 'lead_id');
    }

    public function pop_messages(): HasMany
    {
        return $this->hasMany(PopMessage::class, 'member_id');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($lead) {
            foreach ($lead->memberships as $key => $membership) {
                $membership->invoice()->delete();
                $membership->reminders()->delete();
                $membership->delete();
            }

            $lead->user ? $lead->user()->delete() : '';
            $lead->leadReminders()->delete();
            $lead->reminderHistory ? $lead->reminderHistory()->delete() : '';
        });
    }

    public function hasCurrentMembership($pricelist)
    {
        if ($this->whereHas('memberships', fn ($q) => $q->whereRelation('service_pricelist.service.service_type', 'main_service', 1))->whereHas('memberships', fn ($q) => $q->whereRelation('service_pricelist.service.service_type', 'id', $pricelist->service->service_type)->where('status', 'current'))->first()) {
            return false;
        }
    }
}
