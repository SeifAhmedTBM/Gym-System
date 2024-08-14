<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public $table = 'services';

    CONST SALES_COMMISSIONS = [
        false => 'No', 
        true => 'Yes'
    ];

    public CONST EXPIRY_TYPES = [
        'days'          => 'Days',
        'months'        => 'Months'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'order',
        'expiry',
        'service_type_id',
        'status',
        'sales_commission',
        'type',
        'trainer',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function service_type()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id')->withTrashed();
    }

    public function service_pricelist()
    {
        return $this->hasMany(Pricelist::class, 'service_id');
    }

    public function memberships()
    {
        return $this->hasManyThrough(Membership::class ,Pricelist::class,'service_id','service_pricelist_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
