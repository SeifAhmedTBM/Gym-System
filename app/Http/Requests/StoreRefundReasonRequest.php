<?php

namespace App\Http\Requests;

use App\Models\RefundReason;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreRefundReasonRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('refund_reason_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:255',
                'required',
                'unique:refund_reasons',
            ],
        ];
    }
}
