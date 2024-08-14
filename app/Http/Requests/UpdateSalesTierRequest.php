<?php

namespace App\Http\Requests;

use App\Models\SalesTier;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSalesTierRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('sales_tier_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:191',
                'required',
            ],
            'commission' => [
                'bail',
                'required',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
