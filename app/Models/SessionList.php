<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class SessionList extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public $table = 'session_lists';


    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = ['current_ranking'];

    protected $fillable = [
        'name',
        'service_id',
        'max_capacity',
        'paid',
        'color',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function trainer_attendants(): HasManyThrough
    {
        return $this->hasManyThrough(TrainerAttendant::class, Schedule::class, 'session_id', 'schedule_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'session_id', 'id');
    }

 

    public function schedule_mains()
    {
        return $this->hasMany(ScheduleMain::class, 'session_id', 'id');
    }

    public function getCurrentRankingAttribute()
    {
        if ($this->max_capacity == 0) {
            return 'UNRANKED';
        }
        return round(($this->trainer_attendants->count() / $this->max_capacity) * 100);
    }
}
