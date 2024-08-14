<?php

namespace App\Http\Requests;

use App\Models\Refund;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateRefundRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('refund_edit');
    }

    public function rules()
    {
        return [
            'amount' => [
                'required',
            ],
            'created_at' => [
                'required',
            ],
        ];
    }
}
