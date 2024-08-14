<?php

namespace App\Http\Requests;

use App\Models\SalesTier;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreSalesTierRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('sales_tier_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'bail',
                'required',
                'string',
                'max:191'
            ],
            'range_from' => [
                'required',
                'array',
                'min:1'
            ],
            'range_to' => [
                'required',
                'array',
                'min:1'
            ],
            'range_from.*' => [
                'required',
                'string',
                'distinct'
            ],
            'range_to.*' => [
                'required',
                'string',
                'distinct'
            ],
            'users' => [
                'required'
            ],
            'commission' => [
                'bail',
                'required',
            ],
            'month' => [
                'bail',
                'required',
                'date:Y-m'
            ],
            'type' => [
                'required',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
