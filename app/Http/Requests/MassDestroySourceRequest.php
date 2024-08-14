<?php

namespace App\Http\Requests;

use App\Models\Source;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroySourceRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('source_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:sources,id',
        ];
    }
}
