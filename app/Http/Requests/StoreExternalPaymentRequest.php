<?php

namespace App\Http\Requests;

use App\Models\ExternalPayment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreExternalPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('external_payment_create');
    }

    public function rules()
    {
        return [
            // 'title' => [
            //     'string',
            //     'max:255',
            //     'required',
            // ],
            'amount' => [
                'required',
            ],
            'notes' => [
                'nullable',
            ],
            'account_id' => [
                'required',
                'integer',
            ],
            'lead_id' => [
                'nullable',
                'exists:leads,id'
            ],
            'external_payment_category_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
