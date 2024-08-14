<?php

namespace App\Http\Requests;

use App\Models\Locker;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreLockerRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('locker_create');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'max:191',
                'required',
            ],
        ];
    }
}
