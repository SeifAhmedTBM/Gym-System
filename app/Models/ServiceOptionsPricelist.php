<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceOptionsPricelist extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public $table = 'service_options_pricelists';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'service_option_id',
        'pricelist_id',
        'count',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function service_option()
    {
        return $this->belongsTo(ServiceOption::class, 'service_option_id');
    }

    public function pricelist()
    {
        return $this->belongsTo(Pricelist::class, 'pricelist_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
