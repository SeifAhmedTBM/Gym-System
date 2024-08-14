<?php

namespace App\Http\Requests;

use App\Models\Schedule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateScheduleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('schedule_edit');
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
            ],
            'timeslot_id' => [
                'required',
                'integer',
            ],
            'trainer_id' => [
                'required',
                'integer',
            ],
            'comission_type' => [
                'required',
            ],
            'comission_amount' => [
                'required',
                'integer',
            ],
        ];
    }
}
