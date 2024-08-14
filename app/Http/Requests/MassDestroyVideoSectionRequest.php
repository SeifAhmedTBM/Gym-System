<?php

namespace App\Http\Requests;

use App\Models\VideoSection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyVideoSectionRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('video_section_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:video_sections,id',
        ];
    }
}
