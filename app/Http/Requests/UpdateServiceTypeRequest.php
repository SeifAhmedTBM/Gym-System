<?php

namespace App\Http\Requests;

use App\Models\ServiceType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateServiceTypeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('service_type_edit');
    }

    public function rules()
    {
        return [
            'name'            => [
                'string',
                'max:191',
                'required',
            ],
            'description'     => [
                // 'string'
            ]
        ];
    }
}
