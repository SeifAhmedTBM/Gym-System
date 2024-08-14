<?php

namespace App\Http\Requests;

use App\Models\Payment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('payment_edit');
    }

    public function rules()
    {
        return [
            'account_id' => [
                'required',
            ],
            // 'sales_by_id' => [
            //     'required',
            //     'integer',
            // ],
        ];
    }
}
