<?php

namespace App\Models;

use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = ['member_id','lead_id','membership_id'];

    public function member()
    {
        return $this->belongsTo(Lead::class,'member_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class,'lead_id');
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class,'membership_id');
    }

    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\Invitation');
    }
}
