<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVacationRequest;
use App\Http\Requests\UpdateVacationRequest;
use App\Http\Resources\Admin\VacationResource;
use App\Models\Vacation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VacationsApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('vacation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new VacationResource(Vacation::with(['employee', 'created_by'])->get());
    }

    public function store(StoreVacationRequest $request)
    {
        $vacation = Vacation::create($request->all());

        return (new VacationResource($vacation))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Vacation $vacation)
    {
        abort_if(Gate::denies('vacation_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new VacationResource($vacation->load(['employee', 'created_by']));
    }

    public function update(UpdateVacationRequest $request, Vacation $vacation)
    {
        $vacation->update($request->all());

        return (new VacationResource($vacation))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Vacation $vacation)
    {
        abort_if(Gate::denies('vacation_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vacation->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
