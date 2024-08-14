<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;
    use HasFactory;

    public $table = 'warehouses';

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

    public function warehouseProducts()
    {
        return $this->hasMany(WarehouseProduct::class,'warehouse_id');
    }

    public function warehouseProduct()
    {
        return $this->hasOne(WarehouseProduct::class,'warehouse_id');
    }

    public function products()
    {
        return $this->hasManyThrough(WarehouseProduct::class,Product::class,'id','product_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
