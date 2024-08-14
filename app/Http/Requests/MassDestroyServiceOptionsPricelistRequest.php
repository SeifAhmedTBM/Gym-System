<?php

namespace App\Http\Requests;

use App\Models\ServiceOptionsPricelist;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyServiceOptionsPricelistRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('service_options_pricelist_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:service_options_pricelists,id',
        ];
    }
}
