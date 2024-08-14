<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleMain extends Model
{
    use HasFactory;

    public $table = 'schedule_mains';

    public const STATUS = [
        'active'        => 'Active',
        'inactive'      => 'Inactive',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'date',
        'session_id',
        'timeslot_id',
        'commission_amount',
        'commission_type',
        'trainer_id',
        'schedule_main_id',
        'schedule_main_group_id',
        'status',
        'branch_id',
        'created_at',
        'updated_at',
    ];

    public function schedule_main_group(): BelongsTo
    {
        return $this->belongsTo(ScheduleMainGroup::class, 'schedule_main_group_id');
    }
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }


    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'schedule_main_id');
    }

    public function membership_schedules(): HasMany
    {
        return $this->hasMany(MembershipSchedule::class, 'schedule_main_id');
    }

    public function session()
    {
        return $this->belongsTo(SessionList::class, 'session_id');
    }

    public function timeslot()
    {
        return $this->belongsTo(Timeslot::class, 'timeslot_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
