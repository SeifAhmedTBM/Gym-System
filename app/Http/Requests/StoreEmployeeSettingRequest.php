<?php

namespace App\Http\Requests;

use App\Models\EmployeeSetting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreEmployeeSettingRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('employee_setting_create');
    }

    public function rules()
    {
        return [
            'start_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'end_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'start_time' => [
                'required',
                'date_format:' . config('panel.time_format'),
            ],
            'end_time' => [
                'required',
                'date_format:' . config('panel.time_format'),
            ],
            'default_month_days' => [
                'numeric',
                'required',
            ],
            'default_vacation_days' => [
                'numeric',
                'required',
            ],
        ];
    }
}
