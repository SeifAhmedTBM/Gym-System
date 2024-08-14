<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberRemindersHistory extends Model
{
    use HasFactory;

    protected $fillable = ['lead_id','due_date','action_date','member_status_id','notes','user_id'];

    public function status()
    {
        return $this->belongsTo(MemberStatus::class,'member_status_id');
    }
}
