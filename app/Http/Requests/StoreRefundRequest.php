<?php

namespace App\Http\Requests;

use App\Models\Refund;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreRefundRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('refund_create');
    }

    public function rules()
    {
        return [
            'refund_reason_id' => [
                'required',
                'integer',
            ],
            'invoice_id' => [
                'required',
                'integer',
            ],
            'amount' => [
                'required',
            ],
            'created_by_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
