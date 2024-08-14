<?php

namespace App\Http\Requests;

use App\Models\Loan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreLoanRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('loan_create');
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
            'amount' => [
                'required',
            ],
        ];
    }
}
