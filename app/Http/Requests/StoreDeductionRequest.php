<?php

namespace App\Http\Requests;

use App\Models\Deduction;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreDeductionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('deduction_create');
    }

    public function rules()
    {
        return [
            'employee_id' => [
                'required',
                'integer',
            ],
           
            'reason' => [
                'required',
            ],
            'amount' => [
                'required',
            ]
        ];
    }
}
