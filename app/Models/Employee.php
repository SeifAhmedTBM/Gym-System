<?php

namespace App\Models;

use Carbon\Carbon;
use \DateTimeInterface;
use App\Traits\Auditable;
use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
class Employee extends Model implements HasMedia
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    use InteractsWithMedia;

    public const COLORS = [
        'Sat' => '#8946A6',
        'Sun' => '#C74B50',
        'Mon' => '#062C30',
        'Tue' => '#890F0D',
        'Wed' => '#051367',
        'Thu' => '#361500',
        'Fri' => '#980F5A',
    ];

    public const CARD_CHECK_SELECT = [
        'no'  => 'No',
        'yes' => 'Yes',
    ];

    public const STATUS_SELECT = [
        'active'   => 'Active',
        'inactive' => 'Inactive',
    ];
    public const MOBILE_STATUS_SELECT = [
        1   => 'Active',
        0 => 'Inactive',
    ];

    public const JOB_STATUS_SELECT = [
        'fulltime' => 'Full-time',
        'parttime' => 'Part-time',
    ];

    public $table = 'employees';

    protected $dates = [
        'start_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'order',
        'national',
        'job_status',
        'start_date',
        'attendance_check',
        'salary',
        'name',
        'phone',
        'finger_print_id',
        'status',
        'target_amount',
        'user_id',
        'access_card',
        'branch_id',
        'vacations_balance',
        'created_at',
        'updated_at',
        'deleted_at',
        'mobile_visibility'
    ];
//    Profile image
    protected $appends = [
        'photo',
        'profile_photo',
    ];
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }


    public function getProfilePhotoAttribute()
    {
        $file = $this->getMedia('photo')->last();
        if ($file) {

            return [
                "url"=> $file->getUrl(),
                "thumbnail"=> $file->getUrl('thumb'),
                "preview"=> $file->getUrl('preview'),
            ];
        }
        return [
            "url"=> null,
            "thumbnail"=>null,
            "preview"=>null,
        ];

    }
    public function getPhotoAttribute()
    {
        $file = $this->getMedia('photo')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }

        return $file;
    }


    public function getStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function days() : HasMany
    {
        return $this->hasMany(EmployeeSchedule::class, 'employee_id', 'id');
    }

    public function attendances() : HasMany
    {
        return $this->hasMany(EmployeeAttendance::class, 'finger_print_id', 'finger_print_id');
    }

    public function deductions() : HasMany
    {
        return $this->hasMany(Deduction::class, 'employee_id', 'id');
    }

    public function bonuses() : HasMany
    {
        return $this->hasMany(Bonu::class, 'employee_id', 'id');
    }

    public function loans() : HasMany
    {
        return $this->hasMany(Loan::class, 'employee_id', 'id');
    }

    public function vacations() : HasMany
    {
        return $this->hasMany(Vacation::class, 'employee_id', 'id');
    }
    public function documents() : HasMany
    {
        return $this->hasMany(Document::class, 'employee_id', 'id');
    }

    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\Employee');
    }

    public function payroll() : HasMany
    {
        return $this->hasMany(Payroll::class, 'employee_id', 'id');
    }

    public function branch() : BelongsTo
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }
}
