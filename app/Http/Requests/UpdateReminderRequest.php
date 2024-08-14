<?php

namespace App\Http\Requests;

use App\Models\Reminder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateReminderRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('reminder_edit');
    }

    public function rules()
    {
        return [
            'due_date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'lead_id' => [
                'required',
                'integer',
            ],
            'user_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
