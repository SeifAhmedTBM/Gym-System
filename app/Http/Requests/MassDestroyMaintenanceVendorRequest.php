<?php

namespace App\Http\Requests;

use App\Models\MaintenanceVendor;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyMaintenanceVendorRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('maintenance_vendor_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:maintenance_vendors,id',
        ];
    }
}
