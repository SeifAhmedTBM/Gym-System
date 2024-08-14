<?php

namespace App\Http\Requests;

use App\Models\SalesPlan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreSalesPlanRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('sales_plan_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:191',
                'required',
            ],
            'date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
        ];
    }
}
