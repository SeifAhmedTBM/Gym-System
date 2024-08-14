<?php

namespace App\Http\Requests;

use App\Models\Expense;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('expense_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:191',
                'required',
            ],
            'date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'account_id' => [
                'required',
            ],
            'amount' => [
                'required',
            ],
            'expenses_category_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
