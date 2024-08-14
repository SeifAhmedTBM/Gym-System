<?php

namespace App\Http\Requests;

use App\Models\Schedule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreScheduleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('schedule_create');
    }

    public function rules()
    {
        return [
            'session_id' => [
                'required',
                'integer',
            ],
            'day' => [
                'required',
            ],
            'date' => [
                'required',
                'date_format:Y-m',
            ],
            'timeslot_id' => [
                'required',
                'integer',
            ],
            'trainer_id' => [
                'required',
                'integer',
            ],
            'comission_amount' => [
                'required',
                'integer',
            ],
            'comission_type' => [
                'required',
            ],
        ];
    }
}
