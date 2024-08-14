<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = ['text', 'image', 'created_by'];

    public const TYPES = [
        'sms' => 'SMS',
        'whatsapp' => 'Whatsapp'
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
