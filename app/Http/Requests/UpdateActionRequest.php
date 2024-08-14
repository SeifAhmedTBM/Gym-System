<?php

namespace App\Http\Requests;

use App\Models\Action;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateActionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('action_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:191',
                'required',
                'unique:actions,name,' . request()->route('action')->id,
            ],
        ];
    }
}
