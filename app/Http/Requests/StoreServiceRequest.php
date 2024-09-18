<?php

namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreServiceRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('service_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:191',
                'required',
            ],
            'expiry' => [
                'numeric',
                'required',
            ],
            'service_type_id' => [
                'required',
                'integer',
            ],
            'cover' => [
                'nullable',
            ],
            'logo' => [
                'nullable',
            ],
        ];
    }
}
