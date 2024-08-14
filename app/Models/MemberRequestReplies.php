<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberRequestReplies extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    public function member_request() : BelongsTo
    {
        return $this->belongsTo(MemberRequest::class, 'member_request_id', 'id');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
