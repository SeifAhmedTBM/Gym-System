<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pricelist extends Model
{

    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public $table = 'pricelists';

    public CONST MAIN_SERVICE = [
        true => 'Yes',
        false => 'No',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'order',
        'name',
        'amount',
        'service_id',
        'status',
        'from',
        'to',
        'upgrade_from',
        'upgrade_to',
        'expiring_date',
        'expiring_session',
        'freeze_count',
        'invitation',
        'free_sessions',
        'session_count',
        'spa_count',
        'full_day',
        'followup_date',
        'upgrade_date',
        'branch_id',
        'all_branches',
        'main_service',
        'created_at',
        'updated_at',
        'deleted_at',
        'max_count',
    ];
    

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function serviceOptionsPricelist()
    {
        return $this->hasMany(ServiceOptionsPricelist::class,'pricelist_id');
    }

    public function membership()
    {
        return $this->hasOne(Membership::class,'service_pricelist_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function memberships() : HasMany
    {
        return $this->hasMany(Membership::class, 'service_pricelist_id', 'id');
    }

    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class,Membership::class,'service_pricelist_id','membership_id');
    }

    public function pricelist_days() : HasMany
    {
        return $this->hasMany(PricelistDays::class,'pricelist_id');
    }

    public function branches(){
        if ($this->all_branches == "true"){
            return Branch::whereNull('deleted_at')->get(['name','id']);
        }
        return Branch::where('id',$this->branch_id)->get(['name','id']);
        
    }
}
