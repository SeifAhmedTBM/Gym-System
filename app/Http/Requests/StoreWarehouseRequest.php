<?php

namespace App\Http\Requests;

use App\Models\Warehouse;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('warehouse_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:255',
                'required',
                'unique:warehouses',
            ],
        ];
    }
}
