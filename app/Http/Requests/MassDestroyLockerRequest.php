<?php

namespace App\Http\Requests;

use App\Models\Locker;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyLockerRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('locker_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:lockers,id',
        ];
    }
}
