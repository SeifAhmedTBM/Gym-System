<?php

namespace App\Models;

use Carbon\Carbon;
use \DateTimeInterface;
use App\Models\Setting;
use App\Traits\Auditable;
use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FreezeRequest extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public const STATUS_SELECT = [
        'confirmed' => 'Confirmed',
        'pending'   => 'Pending',
        'rejected'  => 'Rejected',
    ];

    public const STATUS_COLOR = [
        'confirmed' => 'success',
        'pending'   => 'warning',
        'rejected'  => 'danger',
    ];

    public const IS_RETROACTIVE = [
        True        => 'Yes',
        False       => 'No',
    ];

    public const IS_RETROACTIVE_COLOR = [
        True        => 'success',
        False       => 'danger',
    ];

    public $table = 'freeze_requests';

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'membership_id',
        'freeze',
        'start_date',
        'end_date',
        'status',
        'created_by_id',
        'is_retroactive',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function membership()
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }

    public function getStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getEndDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function freezeDuration()
    {
        return Setting::first()->freeze_duration;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\FreezeRequest');
    }
}
