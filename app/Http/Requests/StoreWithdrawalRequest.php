<?php

namespace App\Http\Requests;

use App\Models\Withdrawal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreWithdrawalRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('withdrawal_create');
    }

    public function rules()
    {
        return [
            'amount' => [
                'required',
            ],
            'notes' => [
                'required',
            ],
            'account_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
