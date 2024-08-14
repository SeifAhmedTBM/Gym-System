<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RulesAndPolicy extends Model
{
    use HasFactory;

    protected $fillable = ['description','created_at','updated_at'];
}
