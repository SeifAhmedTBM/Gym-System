<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;
    
    public $table = 'tasks';

    const STATUS = [
        'pending'               => 'Pending',
        'in_progress'           => 'In Progress',
        'done'                  => 'Done (without confirmation)',
        'done_with_confirm'     => 'Done (with confirmation)'
    ];
    const STATUS_COLOR = [
        'pending'               => 'warning',
        'in_progress'           => 'info',
        'done'                  => 'success',
        'done_with_confirm'     => 'primary'
    ];
    protected $fillable = [
        'title',
        'description',
        'to_user_id',
        'to_role_id',
        'created_by_id',
        'done_at',
        'confirmation_at',
        'task_date',
        'status',
        'supervisor_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function to_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
    
    public function to_role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'to_role_id');
    }

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
