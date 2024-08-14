<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timeslot extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public $table = 'timeslots';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'from',
        'to',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id', 'id');
    }

    public function schedules() : HasMany
    {
        return $this->hasMany(Schedule::class, 'timeslot_id', 'id');
    }
}
