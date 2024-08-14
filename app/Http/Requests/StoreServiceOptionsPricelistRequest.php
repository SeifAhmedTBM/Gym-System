<?php

namespace App\Http\Requests;

use App\Models\ServiceOptionsPricelist;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreServiceOptionsPricelistRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('service_options_pricelist_create');
    }

    public function rules()
    {
        return [
            'service_option_id' => [
                'required',
                'integer',
            ],
            'pricelist_id' => [
                'required',
                'integer',
            ]
        ];
    }
}
