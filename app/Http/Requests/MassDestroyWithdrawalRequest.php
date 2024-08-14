<?php

namespace App\Http\Requests;

use App\Models\Withdrawal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyWithdrawalRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('withdrawal_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:withdrawals,id',
        ];
    }
}
