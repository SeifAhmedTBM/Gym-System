<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('employee_edit');
    }

    public function rules()
    {
        return [
            // 'job_status' => [
            //     'required',
            // ],
            // 'start_date' => [
            //     'required',
            //     'date_format:' . config('panel.date_format'),
            // ],
            // 'attendance_check' => [
            //     'bail',
            //     'required'
            // ],
            'salary' => [
                'required',
            ],
            // 'status' => [
            //     'required',
            // ],
            'name' => [
                'bail',
                'required',
                'string',
                'max:191',
                'unique:employees,name,'.request()->employee_id
            ],
            'phone' => [
                'bail',
                'required',
                'numeric',
                'unique:employees,phone,'.request()->employee_id.',id,deleted_at,NULL'
            ],
            'target_amount' => [
                'nullable'
            ],
            'image' => [
                'nullable'
            ],
            // 'finger_print_id' => [
            //     'nullable',
            //     'required_if:status,active',
            //     'numeric',
            //     'unique:employees,finger_print_id,'.request()->employee_id.',id,deleted_at,NULL'
            // ],
            // 'access_card' => [
            //     'nullable',
            //     'required_if:status,active',
            //     'unique:employees,access_card,'.request()->employee_id.',id,deleted_at,NULL'
            // ],
        ];
    }
}
