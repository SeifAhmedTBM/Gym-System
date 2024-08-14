<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceTypeRequest;
use App\Http\Requests\UpdateServiceTypeRequest;
use App\Http\Resources\Admin\ServiceTypeResource;
use App\Models\ServiceType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceTypesApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('service_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ServiceTypeResource(ServiceType::all());
    }

    public function store(StoreServiceTypeRequest $request)
    {
        $serviceType = ServiceType::create($request->all());

        return (new ServiceTypeResource($serviceType))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ServiceType $serviceType)
    {
        abort_if(Gate::denies('service_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ServiceTypeResource($serviceType);
    }

    public function update(UpdateServiceTypeRequest $request, ServiceType $serviceType)
    {
        $serviceType->update($request->all());

        return (new ServiceTypeResource($serviceType))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(ServiceType $serviceType)
    {
        abort_if(Gate::denies('service_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $serviceType->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
