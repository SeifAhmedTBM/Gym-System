<?php

namespace App\Http\Requests;

use App\Models\AssetType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateAssetTypeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('asset_type_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:191',
                'required',
            ],
        ];
    }
}
