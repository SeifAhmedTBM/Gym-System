<?php

namespace App\Models;

use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadRemindersHistory extends Model
{
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

    protected $fillable = ['lead_id', 'due_date', 'action_date', 'status_id', 'notes', 'user_id', 'type', 'membership_id', 'action'];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }

    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\LeadRemindersHistory');
    }
}
