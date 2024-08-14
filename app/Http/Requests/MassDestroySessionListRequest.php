<?php

namespace App\Http\Requests;

use App\Models\SessionList;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroySessionListRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('session_list_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:session_lists,id',
        ];
    }
}
