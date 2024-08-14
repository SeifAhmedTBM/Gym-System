<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesTier extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public const STATUS_SELECT = [
        'active'   => 'Active',
        'inactive' => 'Inactive',
    ];

    public const TYPE_SELECT = [
        'sales'      => 'Sales',
        'trainer'    => 'Trainer',
        'freelancer' => 'Freelancer',
    ];

    public $table = 'sales_tiers';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'type',
        'month',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function sales_tiers_ranges() : HasMany
    {
        return $this->hasMany(SalesTiersRange::class, 'sales_tier_id', 'id');
    }

    public function sales_tiers_users() : HasMany
    {
        return $this->hasMany(SalesTiersUser::class, 'sales_tier_id', 'id');
    }
}
