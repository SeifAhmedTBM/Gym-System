<?php

namespace App\Http\Requests;

use App\Models\Hotdeal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyHotdealRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('hotdeal_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:hotdeals,id',
        ];
    }
}
