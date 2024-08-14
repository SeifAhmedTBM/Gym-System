<?php

namespace App\Http\Requests;

use App\Models\ExpensesCategory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyExpensesCategoryRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('expenses_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:expenses_categories,id',
        ];
    }
}
