<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExternalPaymentRequest;
use App\Http\Requests\UpdateExternalPaymentRequest;
use App\Http\Resources\Admin\ExternalPaymentResource;
use App\Models\ExternalPayment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExternalPaymentApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('external_payment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ExternalPaymentResource(ExternalPayment::with(['account', 'created_by'])->get());
    }

    public function store(StoreExternalPaymentRequest $request)
    {
        $externalPayment = ExternalPayment::create($request->all());

        return (new ExternalPaymentResource($externalPayment))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ExternalPayment $externalPayment)
    {
        abort_if(Gate::denies('external_payment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ExternalPaymentResource($externalPayment->load(['account', 'created_by']));
    }

    public function update(UpdateExternalPaymentRequest $request, ExternalPayment $externalPayment)
    {
        $externalPayment->update($request->all());

        return (new ExternalPaymentResource($externalPayment))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(ExternalPayment $externalPayment)
    {
        abort_if(Gate::denies('external_payment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $externalPayment->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
