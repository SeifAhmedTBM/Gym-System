<?php

namespace App\Http\Requests;

use App\Models\ServiceOption;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreServiceOptionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('service_option_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:255',
                'required',
                'unique:service_options',
            ],
        ];
    }
}
