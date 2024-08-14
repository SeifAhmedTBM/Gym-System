<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSalesTierRequest;
use App\Http\Requests\UpdateSalesTierRequest;
use App\Http\Resources\Admin\SalesTierResource;
use App\Models\SalesTier;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SalesTiersApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('sales_tier_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SalesTierResource(SalesTier::all());
    }

    public function store(StoreSalesTierRequest $request)
    {
        $salesTier = SalesTier::create($request->all());

        return (new SalesTierResource($salesTier))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(SalesTier $salesTier)
    {
        abort_if(Gate::denies('sales_tier_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SalesTierResource($salesTier);
    }

    public function update(UpdateSalesTierRequest $request, SalesTier $salesTier)
    {
        $salesTier->update($request->all());

        return (new SalesTierResource($salesTier))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(SalesTier $salesTier)
    {
        abort_if(Gate::denies('sales_tier_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesTier->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
