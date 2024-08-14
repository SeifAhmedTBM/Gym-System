<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rating extends Model
{
    use HasFactory;

    public const STARS = [
        '1' => "<i class='fa fa-star text-warning'></i>",
        '2' => "<i class='fa fa-star text-warning'></i> <i class='fa fa-star text-warning'></i>",
        '3' => "<i class='fa fa-star text-warning'></i> <i class='fa fa-star text-warning'></i> <i class='fa fa-star text-warning'></i>",
        '4' => "<i class='fa fa-star text-warning'></i> <i class='fa fa-star text-warning'></i> <i class='fa fa-star text-warning'></i> <i class='fa fa-star text-warning'></i>",
        '5' => "<i class='fa fa-star text-warning'></i> <i class='fa fa-star text-warning'></i> <i class='fa fa-star text-warning'></i> <i class='fa fa-star text-warning'></i> <i class='fa fa-star text-warning'></i>",
    ];
    
    public $table = 'ratings';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'trainer_id',
        'member_id',
        'rate',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    
    public function member()
    {
        return $this->belongsTo(Lead::class,'member_id');
    }
}
