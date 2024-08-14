<?php

namespace App\Http\Requests;

use App\Models\MembershipAttendance;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateMembershipAttendanceRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('membership_attendance_edit');
    }

    public function rules()
    {
        return [
            'sign_in' => [
                // 'date_format:' . config('panel.time_format'),
                'nullable',
            ],
            'sign_out' => [
                // 'date_format:' . config('panel.time_format'),
                'nullable',
            ]
        ];
    }
}
