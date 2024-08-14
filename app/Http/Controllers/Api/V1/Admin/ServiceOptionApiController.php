<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceOptionRequest;
use App\Http\Requests\UpdateServiceOptionRequest;
use App\Http\Resources\Admin\ServiceOptionResource;
use App\Models\ServiceOption;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceOptionApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('service_option_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ServiceOptionResource(ServiceOption::all());
    }

    public function store(StoreServiceOptionRequest $request)
    {
        $serviceOption = ServiceOption::create($request->all());

        return (new ServiceOptionResource($serviceOption))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ServiceOption $serviceOption)
    {
        abort_if(Gate::denies('service_option_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ServiceOptionResource($serviceOption);
    }

    public function update(UpdateServiceOptionRequest $request, ServiceOption $serviceOption)
    {
        $serviceOption->update($request->all());

        return (new ServiceOptionResource($serviceOption))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(ServiceOption $serviceOption)
    {
        abort_if(Gate::denies('service_option_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $serviceOption->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
