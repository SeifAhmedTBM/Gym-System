<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSourceRequest;
use App\Http\Requests\UpdateSourceRequest;
use App\Http\Resources\Admin\SourceResource;
use App\Models\Source;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SourceApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('source_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SourceResource(Source::all());
    }

    public function store(StoreSourceRequest $request)
    {
        $source = Source::create($request->all());

        return (new SourceResource($source))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Source $source)
    {
        abort_if(Gate::denies('source_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SourceResource($source);
    }

    public function update(UpdateSourceRequest $request, Source $source)
    {
        $source->update($request->all());

        return (new SourceResource($source))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Source $source)
    {
        abort_if(Gate::denies('source_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $source->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
