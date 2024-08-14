<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PricelistDays extends Model
{
    use HasFactory;

    public const DAYS = [
        'Sat'       => 'Saturday',
        'Sun'       => 'Sunday',
        'Mon'       => 'Monday',
        'Tue'       => 'Tuesday',
        'Wed'       => 'Wednesday',
        'Thu'       => 'Thuresday',
        'Fri'       => 'Friday',
    ];

    public $table = 'pricelist_days';
    
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'day',
        'pricelist_id',
        'created_at',
        'updated_at',
    ];

    public function pricelist()
    {
        return $this->belongsTo(Pricelist::class,'pricelist_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
