<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PopMessage extends Model
{
    use HasFactory;

    protected $fillable = ['message','created_by_id','member_id'];

    public function member() :BelongsTo
    {
        return $this->belongsTo(Lead::class,'member_id');
    }

    public function created_by() :BelongsTo
    {
        return $this->belongsTo(User::class,'created_by_id');
    }

    public function pop_messages_replies() :HasMany
    {
        return $this->hasMany(PopMessageReply::class,'pop_message_id');
    }
}
