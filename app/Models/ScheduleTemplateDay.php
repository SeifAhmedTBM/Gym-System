<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleTemplateDay extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['day', 'from', 'to', 'is_offday', 'schedule_template_id', 'working_hours', 'flexible'];

    public function template() : BelongsTo
    {
        return $this->belongsTo(ScheduleTemplate::class, 'schedule_template_id', 'id');
    }
}
