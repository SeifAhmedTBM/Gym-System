<?php

namespace App\Http\Requests;

use App\Models\MemberStatus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreMemberStatusRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('member_status_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:191',
                'required',
            ],
            'default_next_followup_days' => [
                'string',
                'max:191',
                'required',
            ],
            'need_followup' => [
                'required',
            ],
        ];
    }
}
