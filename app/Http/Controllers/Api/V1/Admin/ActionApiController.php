<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActionRequest;
use App\Http\Requests\UpdateActionRequest;
use App\Http\Resources\Admin\ActionResource;
use App\Models\Action;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActionApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('action_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ActionResource(Action::all());
    }

    public function store(StoreActionRequest $request)
    {
        $action = Action::create($request->all());

        return (new ActionResource($action))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Action $action)
    {
        abort_if(Gate::denies('action_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ActionResource($action);
    }

    public function update(UpdateActionRequest $request, Action $action)
    {
        $action->update($request->all());

        return (new ActionResource($action))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Action $action)
    {
        abort_if(Gate::denies('action_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $action->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
