<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipSchedule extends Model
{
    use HasFactory;

    public $table = 'membership_schedules';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'membership_id',
        'schedule_main_id',
        'schedule_id',
        'is_active',
        'created_at',
        'updated_at',
    ];

    public function membership() :BelongsTo
    {
        return $this->belongsTo(Membership::class,'membership_id');
    }

    public function schedule() :BelongsTo
    {
        return $this->belongsTo(Schedule::class,'schedule_id');
    }

    public function schedule_main() :BelongsTo
    {
        return $this->belongsTo(ScheduleMain::class,'schedule_main_id');
    }
    
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
