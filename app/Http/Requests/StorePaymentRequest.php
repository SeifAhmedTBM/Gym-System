<?php

namespace App\Http\Requests;

use App\Models\Payment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StorePaymentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('payment_create');
    }

    public function rules()
    {
        return [
            'payment_method' => [
                'required',
            ],
            'invoice_id' => [
                'required',
                'integer',
            ],
            'sales_by_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
