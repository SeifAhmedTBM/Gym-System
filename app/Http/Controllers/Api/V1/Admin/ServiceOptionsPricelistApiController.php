<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceOptionsPricelistRequest;
use App\Http\Requests\UpdateServiceOptionsPricelistRequest;
use App\Http\Resources\Admin\ServiceOptionsPricelistResource;
use App\Models\ServiceOptionsPricelist;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceOptionsPricelistApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('service_options_pricelist_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ServiceOptionsPricelistResource(ServiceOptionsPricelist::with(['service_option', 'pricelist'])->get());
    }

    public function store(StoreServiceOptionsPricelistRequest $request)
    {
        $serviceOptionsPricelist = ServiceOptionsPricelist::create($request->all());

        return (new ServiceOptionsPricelistResource($serviceOptionsPricelist))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ServiceOptionsPricelist $serviceOptionsPricelist)
    {
        abort_if(Gate::denies('service_options_pricelist_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ServiceOptionsPricelistResource($serviceOptionsPricelist->load(['service_option', 'pricelist']));
    }

    public function update(UpdateServiceOptionsPricelistRequest $request, ServiceOptionsPricelist $serviceOptionsPricelist)
    {
        $serviceOptionsPricelist->update($request->all());

        return (new ServiceOptionsPricelistResource($serviceOptionsPricelist))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(ServiceOptionsPricelist $serviceOptionsPricelist)
    {
        abort_if(Gate::denies('service_options_pricelist_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $serviceOptionsPricelist->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
