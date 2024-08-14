<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerSessionAttendance extends Model
{
    public $table = 'trainer_session_attendances';
    use HasFactory;
    protected $fillable = [
        'trainer_id',
        'schedule_id',
        'date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }
}
