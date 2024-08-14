<?php

namespace App\Models;

use Carbon\Carbon;
use \DateTimeInterface;
use App\Traits\Auditable;
use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reminder extends Model
{
    // use SoftDeletes;
    use Auditable;
    use HasFactory;

    public const TYPE = [
        'sales'             => 'Sales',
        'due_payment'       => 'Due Payment',
        'follow_up'         => 'Follow Up',
        'inactive'          => 'Inactive',
        'upgrade'           => 'Upgrade',
        'expiring'          => 'Expiring',
        'custom'            => 'Custom',
        'welcome_call'      => 'Welcome Call',
        'renew'             => 'Renew',
        'pt_session'        => 'PT Session'
    ];

    public const ACTION = [
        'appointment'       => 'Appointment',
        'follow_up'         => 'Follow Up',
        'maybe'             => 'Maybe',
        'not_interested'    => 'Not Interested',
        'no_answer'         => 'No Answer',
        'done'              => 'Done',
    ];

    public const ACTION_COLOR = [
        'appointment'       => 'info',
        'follow_up'         => 'primary',
        'maybe'             => 'warning',
        'not_interested'    => 'danger',
        'no_answer'         => 'secondary',
        'done'              => 'success',
    ];

    public $table = 'reminders';

    protected $dates = [
        'due_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'type',
        'membership_id',
        'due_date',
        'action',
        'lead_id',
        'user_id',
        'notes',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getDueDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDueDateAttribute($value)
    {
        $this->attributes['due_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\Reminder');
    }
}
