<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBonuRequest;
use App\Http\Requests\UpdateBonuRequest;
use App\Http\Resources\Admin\BonuResource;
use App\Models\Bonu;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BonusesApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('bonu_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new BonuResource(Bonu::with(['employee', 'created_by'])->get());
    }

    public function store(StoreBonuRequest $request)
    {
        $bonu = Bonu::create($request->all());

        return (new BonuResource($bonu))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Bonu $bonu)
    {
        abort_if(Gate::denies('bonu_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new BonuResource($bonu->load(['employee', 'created_by']));
    }

    public function update(UpdateBonuRequest $request, Bonu $bonu)
    {
        $bonu->update($request->all());

        return (new BonuResource($bonu))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Bonu $bonu)
    {
        abort_if(Gate::denies('bonu_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bonu->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
