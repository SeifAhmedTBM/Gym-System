<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRefundReasonRequest;
use App\Http\Requests\UpdateRefundReasonRequest;
use App\Http\Resources\Admin\RefundReasonResource;
use App\Models\RefundReason;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefundReasonsApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('refund_reason_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new RefundReasonResource(RefundReason::all());
    }

    public function store(StoreRefundReasonRequest $request)
    {
        $refundReason = RefundReason::create($request->all());

        return (new RefundReasonResource($refundReason))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(RefundReason $refundReason)
    {
        abort_if(Gate::denies('refund_reason_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new RefundReasonResource($refundReason);
    }

    public function update(UpdateRefundReasonRequest $request, RefundReason $refundReason)
    {
        $refundReason->update($request->all());

        return (new RefundReasonResource($refundReason))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(RefundReason $refundReason)
    {
        abort_if(Gate::denies('refund_reason_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $refundReason->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
