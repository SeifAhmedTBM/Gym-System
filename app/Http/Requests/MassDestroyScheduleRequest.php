<?php

namespace App\Http\Requests;

use App\Models\Schedule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyScheduleRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('schedule_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:schedules,id',
        ];
    }
}
