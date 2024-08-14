<?php

namespace App\Http\Requests;

use App\Models\Document;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('document_edit');
    }

    public function rules()
    {
        return [
            'employee_id' => [
                'required',
                'integer',
            ],
            'name' => [
                'string',
                'max:191',
                'required',
            ],
            'description' => [
                'required',
            ],
            'image' => [
                'required',
            ],
        ];
    }
}
