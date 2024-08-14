<?php

namespace App\Http\Requests;

use App\Models\Bonu;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreBonuRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('bonu_create');
    }

    public function rules()
    {
        return [
            'employee_id' => [
                'required',
                'integer'
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
