<?php

namespace App\Http\Requests;

use App\Models\Vacation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateVacationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('vacation_edit');
    }

    public function rules()
    {
        return [
            'employee_id' => [
                'required',
                'integer',
            ],
            'name' => [
                'string',
                'max:191',
                'required',
            ],
            'description' => [
                'required',
            ],
            'from' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'to' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
        ];
    }
}
