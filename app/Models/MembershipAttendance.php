<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipAttendance extends Model
{
    // use SoftDeletes;
    use Auditable;
    use HasFactory;

    public $table = 'membership_attendances';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'sign_in',
        'sign_out',
        'membership_id',
        'locker',
        'branch_id',
        'created_at',
        'membership_status',
        'updated_at',
        'deleted_at',
    ];

    public function membership()
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }

    public function branch() : BelongsTo
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\MembershipAttendance');
    }
}
