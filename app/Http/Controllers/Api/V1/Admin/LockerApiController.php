<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLockerRequest;
use App\Http\Requests\UpdateLockerRequest;
use App\Http\Resources\Admin\LockerResource;
use App\Models\Locker;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LockerApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('locker_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new LockerResource(Locker::all());
    }

    public function store(StoreLockerRequest $request)
    {
        $locker = Locker::create($request->all());

        return (new LockerResource($locker))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Locker $locker)
    {
        abort_if(Gate::denies('locker_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new LockerResource($locker);
    }

    public function update(UpdateLockerRequest $request, Locker $locker)
    {
        $locker->update($request->all());

        return (new LockerResource($locker))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Locker $locker)
    {
        abort_if(Gate::denies('locker_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $locker->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
