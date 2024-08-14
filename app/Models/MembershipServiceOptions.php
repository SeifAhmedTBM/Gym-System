<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MembershipServiceOptions extends Model
{
    use HasFactory;

    public $table = 'membership_service_options';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'service_option_pricelist_id',
        'membership_id',
        'count'
    ];
    
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
