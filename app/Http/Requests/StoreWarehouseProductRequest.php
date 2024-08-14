<?php

namespace App\Http\Requests;

use App\Models\WarehouseProduct;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreWarehouseProductRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('warehouse_product_create');
    }

    public function rules()
    {
        return [
            'balance' => [
                'numeric',
            ],
        ];
    }
}
