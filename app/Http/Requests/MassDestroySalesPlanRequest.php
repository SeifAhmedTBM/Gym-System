<?php

namespace App\Http\Requests;

use App\Models\SalesPlan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroySalesPlanRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('sales_plan_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:sales_plans,id',
        ];
    }
}
