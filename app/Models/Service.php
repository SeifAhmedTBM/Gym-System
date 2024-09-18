<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
class Service extends Model implements HasMedia
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;
    use InteractsWithMedia;
    public $table = 'services';

    CONST SALES_COMMISSIONS = [
        false => 'No', 
        true => 'Yes'
    ];

    public CONST EXPIRY_TYPES = [
        'days'          => 'Days',
        'months'        => 'Months'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'order',
        'expiry',
        'service_type_id',
        'status',
        'sales_commission',
        'type',
        'trainer',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $appends = [
        'cover',
        'logo',
    ];
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }
    public function getLogoAttribute()
    {
        $file = $this->getMedia('logo')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }
    
        return $file;
    }
    public function getCoverAttribute()
    {
        $file = $this->getMedia('cover')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }
        return $file;
    }


    public function service_type()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id')->withTrashed();
    }

    public function service_pricelist()
    {
        return $this->hasMany(Pricelist::class, 'service_id');
    }

    public function memberships()
    {
        return $this->hasManyThrough(Membership::class ,Pricelist::class,'service_id','service_pricelist_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
