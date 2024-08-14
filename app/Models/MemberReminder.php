<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberReminder extends Model
{
    use HasFactory;

    public $table = 'member_reminders';

    protected $dates = [
        'due_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'due_date',
        'lead_id',
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // public function lead()
    // {
    //     return $this->belongsTo(Lead::class, 'lead_id');
    // }

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }
    
}
