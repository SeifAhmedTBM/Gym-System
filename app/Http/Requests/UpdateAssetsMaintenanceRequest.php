<?php

namespace App\Http\Requests;

use App\Models\AssetsMaintenance;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateAssetsMaintenanceRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('assets_maintenance_edit');
    }

    public function rules()
    {
        return [
            'date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'amount' => [
                'required',
            ],
            'asset_id' => [
                'required',
                'integer',
            ],
            'maintence_vendor_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
