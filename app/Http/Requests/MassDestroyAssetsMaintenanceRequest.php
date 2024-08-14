<?php

namespace App\Http\Requests;

use App\Models\AssetsMaintenance;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyAssetsMaintenanceRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('assets_maintenance_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:assets_maintenances,id',
        ];
    }
}
