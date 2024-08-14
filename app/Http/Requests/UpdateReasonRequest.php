<?php

namespace App\Http\Requests;

use App\Models\Reason;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReasonRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('reason_edit');
    }

    public function rules()
    {
        return [
            'image' => [
                'required',
            ],
            'title' => [
                'required'
            ],
            'description' => [
                'required'
            ],
        ];
    }
}
