<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleMainGroup extends Model
{
    use HasFactory,SoftDeletes;

    public CONST STATUS = [
        'active'        => 'Active',
        'inactive'      => 'Inactive',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'status',
        'created_at',
        'updated_at',
    ];

    public function schedule_mains() :HasMany
    {
        return $this->hasMany(ScheduleMain::class,'schedule_main_group_id');
    }
}
