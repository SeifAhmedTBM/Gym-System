<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaintenanceVendorRequest;
use App\Http\Requests\UpdateMaintenanceVendorRequest;
use App\Http\Resources\Admin\MaintenanceVendorResource;
use App\Models\MaintenanceVendor;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceVendorsApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('maintenance_vendor_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MaintenanceVendorResource(MaintenanceVendor::all());
    }

    public function store(StoreMaintenanceVendorRequest $request)
    {
        $maintenanceVendor = MaintenanceVendor::create($request->all());

        return (new MaintenanceVendorResource($maintenanceVendor))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(MaintenanceVendor $maintenanceVendor)
    {
        abort_if(Gate::denies('maintenance_vendor_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MaintenanceVendorResource($maintenanceVendor);
    }

    public function update(UpdateMaintenanceVendorRequest $request, MaintenanceVendor $maintenanceVendor)
    {
        $maintenanceVendor->update($request->all());

        return (new MaintenanceVendorResource($maintenanceVendor))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(MaintenanceVendor $maintenanceVendor)
    {
        abort_if(Gate::denies('maintenance_vendor_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $maintenanceVendor->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
