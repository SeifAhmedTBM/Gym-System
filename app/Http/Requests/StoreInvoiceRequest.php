<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('invoice_create');
    }

    public function rules()
    {
        return [
            'discount' => [
                'numeric',
                'required',
            ],
            'service_fee' => [
                'required',
            ],
            'net_amount' => [
                'required',
            ],
            'membership_id' => [
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
