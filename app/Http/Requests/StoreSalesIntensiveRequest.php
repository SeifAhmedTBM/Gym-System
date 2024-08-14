<?php

namespace App\Http\Requests;

use App\Models\SalesIntensive;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreSalesIntensiveRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('sales_intensive_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:191',
                'required',
            ],
        ];
    }
}
