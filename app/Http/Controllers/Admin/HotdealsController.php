<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyHotdealRequest;
use App\Http\Requests\StoreHotdealRequest;
use App\Http\Requests\UpdateHotdealRequest;
use App\Models\Hotdeal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class HotdealsController extends Controller
{
    use MediaUploadingTrait;
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('hotdeal_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Hotdeal::query()->select(sprintf('%s.*', (new Hotdeal())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'hotdeal_show';
                $editGate = 'hotdeal_edit';
                $deleteGate = 'hotdeal_delete';
                $crudRoutePart = 'hotdeals';

                return view('partials.datatablesActions', compact(
                'viewGate',
                'editGate',
                'deleteGate',
                'crudRoutePart',
                'row'
            ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('cover', function ($row) {
                if ($photo = $row->cover) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }

                return '';
            });
            $table->editColumn('logo', function ($row) {
                if ($photo = $row->logo) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }

                return '';
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });

            $table->editColumn('promo_code', function ($row) {
                return $row->promo_code ? $row->promo_code : '';
            });

            $table->editColumn('redeem', function ($row) {
                return $row->redeem ? $row->redeem : '';
            });

            $table->editColumn('type', function ($row) {
                return $row->type ? Hotdeal::TYPE_SELECT[$row->type] : '';
            });

            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'cover', 'logo']);

            return $table->make(true);
        }

        return view('admin.hotdeals.index');
    }

    public function create()
    {
        abort_if(Gate::denies('hotdeal_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.hotdeals.create');
    }

    public function store(StoreHotdealRequest $request)
    {
        $hotdeal = Hotdeal::create($request->all());

        if ($request->input('cover', false)) {
            $hotdeal->addMedia(storage_path('tmp/uploads/' . basename($request->input('cover'))))->toMediaCollection('cover');
        }

        if ($request->input('logo', false)) {
            $hotdeal->addMedia(storage_path('tmp/uploads/' . basename($request->input('logo'))))->toMediaCollection('logo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $hotdeal->id]);
        }

        return redirect()->route('admin.hotdeals.index');
    }

    public function edit(Hotdeal $hotdeal)
    {
        abort_if(Gate::denies('hotdeal_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.hotdeals.edit', compact('hotdeal'));
    }

    public function update(UpdateHotdealRequest $request, Hotdeal $hotdeal)
    {
        $hotdeal->update($request->all());

        if ($request->input('cover', false)) {
            if (!$hotdeal->cover || $request->input('cover') !== $hotdeal->cover->file_name) {
                if ($hotdeal->cover) {
                    $hotdeal->cover->delete();
                }
                $hotdeal->addMedia(storage_path('tmp/uploads/' . basename($request->input('cover'))))->toMediaCollection('cover');
            }
        } elseif ($hotdeal->cover) {
            $hotdeal->cover->delete();
        }

        if ($request->input('logo', false)) {
            if (!$hotdeal->logo || $request->input('logo') !== $hotdeal->logo->file_name) {
                if ($hotdeal->logo) {
                    $hotdeal->logo->delete();
                }
                $hotdeal->addMedia(storage_path('tmp/uploads/' . basename($request->input('logo'))))->toMediaCollection('logo');
            }
        } elseif ($hotdeal->logo) {
            $hotdeal->logo->delete();
        }

        return redirect()->route('admin.hotdeals.index');
    }

    public function show(Hotdeal $hotdeal)
    {
        abort_if(Gate::denies('hotdeal_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.hotdeals.show', compact('hotdeal'));
    }

    public function destroy(Hotdeal $hotdeal)
    {
        abort_if(Gate::denies('hotdeal_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $hotdeal->delete();

        return back();
    }

    public function massDestroy(MassDestroyHotdealRequest $request)
    {
        Hotdeal::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('hotdeal_create') && Gate::denies('hotdeal_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Hotdeal();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
