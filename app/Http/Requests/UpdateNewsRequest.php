<?php

namespace App\Http\Requests;

use App\Models\News;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateNewsRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('news_edit');
    }

    public function rules()
    {
        return [
            'image' => [
                'required',
            ],
            'cover' => [
                'required',
            ],
            'title' => [
                'string',
                'max:191',
                'required',
            ],
            'description' => [
                'required',
            ],
            'section_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
