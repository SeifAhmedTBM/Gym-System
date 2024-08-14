<?php

namespace App\Http\Requests;

use App\Models\Warehouse;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('warehouse_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:255',
                'required',
                'unique:warehouses,name,' . request()->route('warehouse')->id,
            ],
        ];
    }
}
