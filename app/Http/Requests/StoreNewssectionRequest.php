<?php

namespace App\Http\Requests;

use App\Models\Newssection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreNewssectionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('newssection_create');
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
