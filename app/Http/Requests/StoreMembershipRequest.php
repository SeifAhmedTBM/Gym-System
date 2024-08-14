<?php

namespace App\Http\Requests;

use App\Models\Membership;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreMembershipRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('membership_create');
    }

    public function rules()
    {
        return [
            'start_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'end_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'member_id' => [
                'required',
                'integer',
            ],
            'service_pricelist_id' => [
                'required',
                'integer',
            ],
          
        ];
    }
}
