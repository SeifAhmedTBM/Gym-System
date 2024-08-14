<?php

namespace App\Models;

use App\Models\PopMessage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PopMessageReply extends Model
{
    use HasFactory;

    protected $fillable = ['reply','created_by_id','pop_message_id'];
    
    public function created_by() :BelongsTo
    {
        return $this->belongsTo(User::class,'created_by_id');
    }

    public function pop_message() :BelongsTo
    {
        return $this->belongsTo(PopMessage::class,'pop_message_id');
    }
}
