<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductTransactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'from_warehouse',
        'to_warehouse',
        'quantity',
        'type',
        'created_by',
        'notes',
    ];

    const type = [
        'in' => 'In',
        'out' => 'Out',
        'transfer' => 'Transfer'
    ];

    const color = [
        'in' => 'badge-success',
        'out' => 'badge-danger',
        'transfer' => 'badge-warning',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    // public function warehouse()
    // {
    //     return $this->belongsTo(Warehouse::class,'to_warehouse');
    // }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class,'from_warehouse');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class,'to_warehouse');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
}
