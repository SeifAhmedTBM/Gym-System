<?php

namespace App\Http\Requests;

use App\Models\FreezeRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreFreezeRequestRequest extends FormRequest
{
    // public function authorize()
    // {
    //     return Gate::allows('freeze_request_create');
    // }

    public function rules()
    {
        return [
            'membership_id' => [
                'required',
                'integer',
            ],
            'freeze' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'start_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'end_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
        ];
    }
}
