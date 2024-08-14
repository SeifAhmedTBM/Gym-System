<?php

namespace App\Models;

use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class TrainerAttendant extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    

    public function member() : BelongsTo
    {
        return $this->belongsTo(Lead::class, 'member_id', 'id');
    }

    public function trainer() : BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id', 'id');
    }

    public function schedule() : BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
    
    public function membership() : BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\TrainerAttendant');
    }
}
