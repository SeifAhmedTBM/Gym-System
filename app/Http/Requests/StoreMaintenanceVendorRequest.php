<?php

namespace App\Http\Requests;

use App\Models\MaintenanceVendor;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreMaintenanceVendorRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('maintenance_vendor_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:191',
                'required',
            ],
            'mobile' => [
                'string',
                'max:15',
                'required',
            ],
        ];
    }
}
