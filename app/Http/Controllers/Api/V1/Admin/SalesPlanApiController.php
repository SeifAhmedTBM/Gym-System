<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSalesPlanRequest;
use App\Http\Requests\UpdateSalesPlanRequest;
use App\Http\Resources\Admin\SalesPlanResource;
use App\Models\SalesPlan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SalesPlanApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('sales_plan_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SalesPlanResource(SalesPlan::all());
    }

    public function store(StoreSalesPlanRequest $request)
    {
        $salesPlan = SalesPlan::create($request->all());

        return (new SalesPlanResource($salesPlan))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(SalesPlan $salesPlan)
    {
        abort_if(Gate::denies('sales_plan_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SalesPlanResource($salesPlan);
    }

    public function update(UpdateSalesPlanRequest $request, SalesPlan $salesPlan)
    {
        $salesPlan->update($request->all());

        return (new SalesPlanResource($salesPlan))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(SalesPlan $salesPlan)
    {
        abort_if(Gate::denies('sales_plan_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesPlan->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
