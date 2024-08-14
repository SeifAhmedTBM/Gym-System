<?php

namespace App\Http\Requests;

use App\Models\Timeslot;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateTimeslotRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('timeslot_edit');
    }

    public function rules()
    {
        return [
            'from' => [
                'required',
            ],
            'to' => [
                'required'
            ],
        ];
    }
}
