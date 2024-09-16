<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public const DAY_SELECT = [
        'Sat'  => 'Saturday',
        'Sun'  => 'Sunday',
        'Mon'  => 'Monday',
        'Tue'  => 'Tuesday',
        'Wed'  => 'Wednesday',
        'Thu'  => 'Thursday',
        'Fri'  => 'Friday',
    ];

    public $table = 'schedules';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'session_id',
        'day',
        'date',
        'timeslot_id',
        'comission_amount',
        'comission_type',
        'trainer_id',
        'schedule_main_id',
        'branch_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

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

    public function member()
    {
        return $this->belongsTo(Lead::class, 'member_id', 'id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function trainer_attendants() : HasMany
    {
        return $this->hasMany(TrainerAttendant::class, 'schedule_id', 'id');
    }

    public function schedule_main() :BelongsTo
    {
        return $this->belongsTo(ScheduleMain::class,'schedule_main_id');
    }

    public function branch() : BelongsTo
    {
        return $this->belongsTo(Branch::Class ,  'branch_id');

    }
}
