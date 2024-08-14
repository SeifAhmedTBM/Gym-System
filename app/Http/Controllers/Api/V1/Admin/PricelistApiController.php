<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePricelistRequest;
use App\Http\Requests\UpdatePricelistRequest;
use App\Http\Resources\Admin\PricelistResource;
use App\Models\Pricelist;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PricelistApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('pricelist_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PricelistResource(Pricelist::with(['service', 'status'])->get());
    }

    public function store(StorePricelistRequest $request)
    {
        $pricelist = Pricelist::create($request->all());

        return (new PricelistResource($pricelist))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Pricelist $pricelist)
    {
        abort_if(Gate::denies('pricelist_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PricelistResource($pricelist->load(['service', 'status']));
    }

    public function update(UpdatePricelistRequest $request, Pricelist $pricelist)
    {
        $pricelist->update($request->all());

        return (new PricelistResource($pricelist))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Pricelist $pricelist)
    {
        abort_if(Gate::denies('pricelist_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pricelist->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
