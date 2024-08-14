<?php

namespace App\Http\Requests;

use App\Models\ExpensesCategory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateExpensesCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('expenses_category_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:191',
                'required',
                'unique:expenses_categories,name,' . request()->route('expenses_category')->id,
            ],
        ];
    }
}
