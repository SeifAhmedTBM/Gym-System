<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('employee_create');
    }

    public function rules()
    {
        return [
            'job_status' => [
                'required',
            ],
            'start_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'attendance_check' => [
                'nullable',
            ],
            'salary' => [
                'required',
            ],
            'status' => [
                'required',
            ],
            'name' => [
                'bail',
                'required',
                'string',
                'max:191',
                'unique:employees'
            ],

            'phone' => [
                'nullable',
                'string',
                'required',
                'unique:employees,phone,'.request()->employee_id.',id,deleted_at,NULL'
            ],
            'email' => [
                'nullable',
                'email',
                'unique:users'
            ],
            'password' => [
                'nullable',
                'confirmed'
            ],
            'target_amount' => [
                'nullable'
            ],
            'finger_print_id' => [
                // 'required',
                'numeric',
                // 'unique:employees,finger_print_id,deleted_at,NULL'
            ],
            'access_card' => [
                // 'required',
                // 'unique:employees,access_card,deleted_at,NULL'
            ],
            'photo' => [
                 'nullable',
                // 'unique:employees,access_card,deleted_at,NULL'
            ],
        ];
    }
}
