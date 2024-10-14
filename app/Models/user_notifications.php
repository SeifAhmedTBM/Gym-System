<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_notifications extends Model
{
    use HasFactory;

    public function notification()
    {
        return $this->belongsTo(notifications::class , 'notification_id');
    }
}
