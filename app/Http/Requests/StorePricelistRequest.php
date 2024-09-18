<?php

namespace App\Http\Requests;

use App\Models\Pricelist;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StorePricelistRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('pricelist_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
            ],
            'amount' => [
                'required',
            ],
            'service_id' => [
                'required',
                'integer',
            ],
            'max_count' => ['nullable']
        ];
    }
}
