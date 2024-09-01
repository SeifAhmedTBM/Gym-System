<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;
use App\Models\SessionList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreSessionListRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\UpdateSessionListRequest;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroySessionListRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SessionListController extends Controller
{
    use CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('session_list_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sessionLists = SessionList::with('service')->get();

        return view('admin.sessionLists.index', compact('sessionLists'));
    }

    public function create()
    {
        abort_if(Gate::denies('session_list_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $services = Service::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.sessionLists.create', compact('services'));
    }

    public function store(StoreSessionListRequest $request)
    {
        $sessionList = SessionList::create([
            'name' => $request['name'],
            'max_capacity' => $request['max_capacity'],
            'paid' => $request['paid'],
            'color' => $request['color'],
            'service_id' => Service::first()->id
        ]);

        $this->created();
        return redirect()->route('admin.session-lists.index');
    }

    public function edit(SessionList $sessionList)
    {
        abort_if(Gate::denies('session_list_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $services = Service::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sessionList->load('service');

        return view('admin.sessionLists.edit', compact('services', 'sessionList'));
    }

    public function update(UpdateSessionListRequest $request, SessionList $sessionList)
    {
        $sessionList->update($request->all());

        $this->updated();
        return redirect()->route('admin.session-lists.index');
    }

    public function show(SessionList $sessionList)
    {
        abort_if(Gate::denies('session_list_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sessionList->load('service');

        return view('admin.sessionLists.show', compact('sessionList'));
    }

    public function destroy(SessionList $sessionList)
    {
        abort_if(Gate::denies('session_list_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sessionList->delete();

        return back();
    }

    public function massDestroy(MassDestroySessionListRequest $request)
    {
        SessionList::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('session_list_create') && Gate::denies('session_list_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new SessionList();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
