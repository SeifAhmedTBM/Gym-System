<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreeSessions extends Model
{
    use HasFactory;

    protected $fillable = ['membership_id','created_by_id','notes'];

    public function membership()
    {
        return $this->belongsTo(Membership::class,'membership_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class,'created_by_id');
    }
}
