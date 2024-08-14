<?php

namespace App\Http\Requests;

use App\Models\Video;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreVideoRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('video_create');
    }

    public function rules()
    {
        return [
            'link' => [
                'string',
                'max:255',
                'required',
            ],
            'section_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
