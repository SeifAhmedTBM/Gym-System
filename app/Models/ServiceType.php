<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceType extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public const MAIN_SERVICE = [
        true => 'Yes',
        false => 'No',
    ];

    public const IS_PT = [
        true => 'Yes',
        false => 'No',
    ];

    public const IS_CLASS = [
        true => 'Yes',
        false => 'No',
    ];
   
    public const SESSION_TYPE = [
        'non_sessions'      => 'Non Sessions',
        'sessions'          => 'Sessions',
        'group_sessions'    => 'Group Sessions',
    ];

    public $table = 'service_types';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'description',
        'session_type',
        'main_service',
        'is_pt',
        'isClass',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }


    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'service_type_id', 'id');
    }

    public function service_pricelists()
    {
        return $this->hasManyThrough(Pricelist::class, Service::class);
    }
}
