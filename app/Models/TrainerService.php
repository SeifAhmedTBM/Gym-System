<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainerService extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const COMMISSION_TYPE = [
        'percentage'        =>      'Percentage',
        'fixed'             =>      'Fixed Amount'
    ];

    protected $guarded = ['id'];

    public function trainer() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function service() : BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
