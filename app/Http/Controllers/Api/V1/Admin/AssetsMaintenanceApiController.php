<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssetsMaintenanceRequest;
use App\Http\Requests\UpdateAssetsMaintenanceRequest;
use App\Http\Resources\Admin\AssetsMaintenanceResource;
use App\Models\AssetsMaintenance;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssetsMaintenanceApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('assets_maintenance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AssetsMaintenanceResource(AssetsMaintenance::with(['asset', 'maintence_vendor'])->get());
    }

    public function store(StoreAssetsMaintenanceRequest $request)
    {
        $assetsMaintenance = AssetsMaintenance::create($request->all());

        return (new AssetsMaintenanceResource($assetsMaintenance))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(AssetsMaintenance $assetsMaintenance)
    {
        abort_if(Gate::denies('assets_maintenance_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AssetsMaintenanceResource($assetsMaintenance->load(['asset', 'maintence_vendor']));
    }

    public function update(UpdateAssetsMaintenanceRequest $request, AssetsMaintenance $assetsMaintenance)
    {
        $assetsMaintenance->update($request->all());

        return (new AssetsMaintenanceResource($assetsMaintenance))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(AssetsMaintenance $assetsMaintenance)
    {
        abort_if(Gate::denies('assets_maintenance_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assetsMaintenance->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
