<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRefundRequest;
use App\Http\Requests\UpdateRefundRequest;
use App\Http\Resources\Admin\RefundResource;
use App\Models\Refund;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefundApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('refund_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new RefundResource(Refund::with(['refund_reason', 'invoice', 'created_by'])->get());
    }

    public function store(StoreRefundRequest $request)
    {
        $refund = Refund::create($request->all());

        return (new RefundResource($refund))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Refund $refund)
    {
        abort_if(Gate::denies('refund_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new RefundResource($refund->load(['refund_reason', 'invoice', 'created_by']));
    }

    public function update(UpdateRefundRequest $request, Refund $refund)
    {
        $refund->update($request->all());

        return (new RefundResource($refund))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Refund $refund)
    {
        abort_if(Gate::denies('refund_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $refund->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
