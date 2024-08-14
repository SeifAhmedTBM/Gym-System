<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackMembership extends Model
{
    use HasFactory;

    protected $fillable = ['membership_id','status'];

    CONST Status = ['new','renew','upgrade','downgrade'];

    public function membership()
    {
        return $this->belongsTo(Membership::class,'membership_id');
    }
}
