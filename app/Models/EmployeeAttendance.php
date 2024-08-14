<?php

namespace App\Models;

use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'finger_print_id',
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'absent',
        'work_time'
    ];

    // public function employee() : BelongsTo
    // {
    //     return $this->belongsTo(Employee::class, 'finger_print_id', 'finger_print_id');
    // }
    public function employee() : BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = date('Y-m-d', strtotime($value));
    }

    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\EmployeeAttendance');
    }
}
