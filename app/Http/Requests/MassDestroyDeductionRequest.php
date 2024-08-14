<?php

namespace App\Http\Requests;

use App\Models\Deduction;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyDeductionRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('deduction_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:deductions,id',
        ];
    }
}
