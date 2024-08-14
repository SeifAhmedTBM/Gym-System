<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'created_by'];

    public function days() : HasMany
    {
        return $this->hasMany(ScheduleTemplateDay::class, 'schedule_template_id', 'id');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
