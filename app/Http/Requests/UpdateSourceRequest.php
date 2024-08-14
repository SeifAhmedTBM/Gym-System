<?php

namespace App\Http\Requests;

use App\Models\Source;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSourceRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('source_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:191',
                'required',
                'unique:sources,name,' . request()->route('source')->id,
            ],
        ];
    }
}
