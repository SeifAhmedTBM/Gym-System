<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSalesIntensiveRequest;
use App\Http\Requests\UpdateSalesIntensiveRequest;
use App\Http\Resources\Admin\SalesIntensiveResource;
use App\Models\SalesIntensive;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SalesIntensiveApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('sales_intensive_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SalesIntensiveResource(SalesIntensive::all());
    }

    public function store(StoreSalesIntensiveRequest $request)
    {
        $salesIntensive = SalesIntensive::create($request->all());

        return (new SalesIntensiveResource($salesIntensive))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(SalesIntensive $salesIntensive)
    {
        abort_if(Gate::denies('sales_intensive_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SalesIntensiveResource($salesIntensive);
    }

    public function update(UpdateSalesIntensiveRequest $request, SalesIntensive $salesIntensive)
    {
        $salesIntensive->update($request->all());

        return (new SalesIntensiveResource($salesIntensive))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(SalesIntensive $salesIntensive)
    {
        abort_if(Gate::denies('sales_intensive_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesIntensive->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
