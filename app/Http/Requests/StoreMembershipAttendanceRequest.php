<?php

namespace App\Http\Requests;

use App\Models\MembershipAttendance;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreMembershipAttendanceRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('membership_attendance_create');
    }

    public function rules()
    {
        return [
            'sign_in' => [
                'date_format:' . config('panel.time_format'),
                'nullable',
            ],
            'sign_out' => [
                'date_format:' . config('panel.time_format'),
                'nullable',
            ]
        ];
    }
}
