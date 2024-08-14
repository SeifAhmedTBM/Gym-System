<?php

namespace App\Http\Requests;

use App\Models\SessionList;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSessionListRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('session_list_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:255',
                'required',
            ],
            'color' => [
                'string',
                'max:255',
                'required',
            ],
            // 'service_id' => [
            //     'required',
            //     'integer',
            // ],
            'max_capacity' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
