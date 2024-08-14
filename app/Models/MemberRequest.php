<?php

namespace App\Models;

use App\Http\Helpers\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MemberRequest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    public const STATUS = [
        'pending'       => 'warning', 
        'approved'      => 'success', 
        'rejected'      => 'danger'
    ];

    public function member() : BelongsTo
    {
        return $this->belongsTo(Lead::class, 'member_id', 'id');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function member_request_replies() : HasMany
    {
        return $this->hasMany(MemberRequestReplies::class, 'member_request_id', 'id');
    }
    
    public function scopeIndex($query, $data)
    {
        return ModelScope::filter($data, 'App\\Models\\MemberRequest');
    }
}
