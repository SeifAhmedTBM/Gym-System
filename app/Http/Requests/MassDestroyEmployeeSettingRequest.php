<?php

namespace App\Http\Requests;

use App\Models\EmployeeSetting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyEmployeeSettingRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('employee_setting_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:employee_settings,id',
        ];
    }
}
