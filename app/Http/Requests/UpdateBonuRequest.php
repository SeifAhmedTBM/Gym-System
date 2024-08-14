<?php

namespace App\Http\Requests;

use App\Models\Bonu;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBonuRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('bonu_edit');
    }

    public function rules()
    {
        return [
            'employee_id' => [
                'required',
                'integer',
            ],
           
            'reason' => [
                'required',
            ],
            'amount' => [
                'required',
            ]
        ];
    }
}
