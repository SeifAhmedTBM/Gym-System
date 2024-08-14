<?php

namespace App\Models;

use App\Models\Lead;
use \DateTimeInterface;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Source extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public $table = 'sources';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class,'source_id');
    }

    public function members()
    {
        return $this->hasMany(Lead::class,'source_id');
    }

    public function memberships() : HasManyThrough
    {
        return $this->hasManyThrough(Membership::class, Lead::class, 'source_id', 'member_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
