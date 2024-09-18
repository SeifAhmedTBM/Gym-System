<?php

namespace App\Http\Requests;

use App\Models\Pricelist;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdatePricelistRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('pricelist_edit');
    }

    public function rules()
    {
        return [
            'amount' => [
                'required',
            ],
            'service_id' => [
                'required',
                'integer',
            ],
            'max_count'=>['nullable']
        ];
    }
}
