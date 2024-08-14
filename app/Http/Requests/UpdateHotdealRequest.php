<?php

namespace App\Http\Requests;

use App\Models\Hotdeal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateHotdealRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('hotdeal_edit');
    }

    public function rules()
    {
        return [
            'cover' => [
                'required',
            ],
            'logo' => [
                'required',
            ],
            'title' => [
                'string',
                'max:191',
                'nullable',
            ],
            'promo_code' => [
                'string',
                'max:191',
                'nullable',
            ],
            'redeem' => [
                'string',
                'max:191',
                'required',
            ],
            'description' => [
                'required',
            ],
        ];
    }
}
