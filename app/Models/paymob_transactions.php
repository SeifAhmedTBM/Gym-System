<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paymob_transactions extends Model
{
    use HasFactory;

    public function membership(){
        return $this->belongsTo(Membership::class , 'membership_id' , 'id');
    }

    public function user(){
        return $this->belongsTo(User::class , 'user_id' , 'id');
    }
}
