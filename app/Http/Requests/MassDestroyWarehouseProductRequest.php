<?php

namespace App\Http\Requests;

use App\Models\WarehouseProduct;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyWarehouseProductRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('warehouse_product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:warehouse_products,id',
        ];
    }
}
