<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = ['lead_id','notes','created_by_id'];

    public function lead()
    {
        return $this->belongsTo(Lead::class,'lead_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class,'created_by_id');
    }
}
