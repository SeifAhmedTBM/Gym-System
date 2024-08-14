<?php

namespace App\Http\Requests;

use App\Models\Timeslot;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreTimeslotRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('timeslot_create');
    }

    public function rules()
    {
        return [
            'from' => [
                'required'
            ],
            'to' => [
                'required'
            ],
        ];
    }
}
