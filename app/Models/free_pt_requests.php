<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class free_pt_requests extends Model
{
    use HasFactory;

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    

}
