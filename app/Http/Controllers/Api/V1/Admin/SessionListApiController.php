<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreSessionListRequest;
use App\Http\Requests\UpdateSessionListRequest;
use App\Http\Resources\Admin\SessionListResource;
use App\Models\SessionList;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionListApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
//        abort_if(Gate::denies('session_list_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SessionListResource(SessionList::with(['service'])->get());
    }

    public function store(StoreSessionListRequest $request)
    {
        $sessionList = SessionList::create($request->all());

        if ($request->input('image', false)) {
            $sessionList->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        return (new SessionListResource($sessionList))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(SessionList $sessionList)
    {
        abort_if(Gate::denies('session_list_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SessionListResource($sessionList->load(['service']));
    }

    public function update(UpdateSessionListRequest $request, SessionList $sessionList)
    {
        $sessionList->update($request->all());

        if ($request->input('image', false)) {
            if (!$sessionList->image || $request->input('image') !== $sessionList->image->file_name) {
                if ($sessionList->image) {
                    $sessionList->image->delete();
                }
                $sessionList->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($sessionList->image) {
            $sessionList->image->delete();
        }

        return (new SessionListResource($sessionList))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(SessionList $sessionList)
    {
        abort_if(Gate::denies('session_list_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sessionList->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
