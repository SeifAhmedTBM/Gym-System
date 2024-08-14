<?php

namespace App\Http\Requests;

use App\Models\MasterCard;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateMasterCardRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('master_card_edit');
    }

    public function rules()
    {
        return [
            'master_card' => [
                'string',
                'required',
                'unique:master_cards,master_card,' . request()->route('master_card')->id,
            ],
        ];
    }
}
