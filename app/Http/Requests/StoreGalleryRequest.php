<?php

namespace App\Http\Requests;

use App\Models\Gallery;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreGalleryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('gallery_create');
    }

    public function rules()
    {
        return [
            'images' => [
                'required',
            ],
            'section_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
