<?php

namespace App\Http\Controllers\Admin;

use App\Models\Reason;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreReasonRequest;
use App\Http\Requests\UpdateReasonRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\MassDestroyReasonRequest;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ReasonsController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('reason_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $reasons = Reason::with(['media'])->get();

        return view('admin.reasons.index', compact('reasons'));
    }

    public function create()
    {
        abort_if(Gate::denies('reason_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.reasons.create');
    }

    public function store(StoreReasonRequest $request)
    {
        
        $reason = Reason::create($request->all());

        if ($request->input('image', false)) {
            $reason->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $reason->id]);
        }

        return redirect()->route('admin.reasons.index');
    }

    public function edit(Reason $reason)
    {
        abort_if(Gate::denies('reason_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.reasons.edit', compact('reason'));
    }

    public function update(UpdateReasonRequest $request, Reason $reason)
    {
        $reason->update($request->all());

        if ($request->input('image', false)) {
            if (!$reason->image || $request->input('image') !== $reason->image->file_name) {
                if ($reason->image) {
                    $reason->image->delete();
                }
                $reason->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($reason->image) {
            $reason->image->delete();
        }

        return redirect()->route('admin.reasons.index');
    }

    public function show(Reason $reason)
    {
        abort_if(Gate::denies('reason_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.reasons.show', compact('reason'));
    }

    public function destroy(Reason $reason)
    {
        abort_if(Gate::denies('reason_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $reason->delete();

        return back();
    }

    public function massDestroy(MassDestroyReasonRequest $request)
    {
        Reason::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('reason_create') && Gate::denies('reason_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Reason();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
