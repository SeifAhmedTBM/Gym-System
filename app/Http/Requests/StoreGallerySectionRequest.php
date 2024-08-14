<?php

namespace App\Http\Requests;

use App\Models\GallerySection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreGallerySectionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('gallery_section_create');
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
