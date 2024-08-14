<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWithdrawalRequest;
use App\Http\Requests\UpdateWithdrawalRequest;
use App\Http\Resources\Admin\WithdrawalResource;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WithdrawalApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('withdrawal_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new WithdrawalResource(Withdrawal::with(['account', 'created_by'])->get());
    }

    public function store(StoreWithdrawalRequest $request)
    {
        $withdrawal = Withdrawal::create($request->all());

        return (new WithdrawalResource($withdrawal))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Withdrawal $withdrawal)
    {
        abort_if(Gate::denies('withdrawal_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new WithdrawalResource($withdrawal->load(['account', 'created_by']));
    }

    public function update(UpdateWithdrawalRequest $request, Withdrawal $withdrawal)
    {
        $withdrawal->update($request->all());

        return (new WithdrawalResource($withdrawal))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Withdrawal $withdrawal)
    {
        abort_if(Gate::denies('withdrawal_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $withdrawal->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
