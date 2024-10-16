<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyServiceRequest;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\Status;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\MobileSetting;
use Illuminate\Support\Facades\Storage;
class ServicesController extends Controller
{
    use CsvImportTrait;
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('service_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $query = Service::with(['service_type']);
            if ($request->St){
            $query = $query->whereHas('service_type', function ($query) use ($request) {
                $query->where('id', $request->St);
            });
            }
            $query = $query->select(sprintf('%s.*', (new Service())->getTable()));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('logo', function ($row) {
                if ($photo = $row->logo) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail ,
                    );
                }

                return '';
            });
            $table->editColumn('actions', function ($row) {
                $viewGate = 'service_show';
                $editGate = 'service_edit';
                $deleteGate = 'service_delete';
                $crudRoutePart = 'services';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->order ? $row->order : '';
            });
            
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            
            $table->editColumn('expiry', function ($row) {
                return $row->expiry ? $row->expiry . ' ' . Service::EXPIRY_TYPES[$row->type] : '';
            });
            
            $table->addColumn('service_type_name', function ($row) {
                return $row->service_type ? $row->service_type->name : '';
            });

            $table->addColumn('status', function ($row) {
                return $row->status ? $row->status : '';
            });

            $table->addColumn('sales_commission', function ($row) {
                return $row->sales_commission == true ? 'Yes' : 'No';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'service_type', 'status','logo']);

            return $table->make(true);
        }

        return view('admin.services.index');
    }

    public function create()
    {
        abort_if(Gate::denies('service_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $service_types = ServiceType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $image_service_id = MobileSetting::all()->first()->classes_service_type;

        return view('admin.services.create', compact('service_types','image_service_id'));
    }

    public function store(StoreServiceRequest $request)
    {
        $service = Service::create($request->all());
        if ($request->input('cover', false)) {
            $service->addMedia(storage_path('tmp/uploads/' . basename($request->input('cover'))))->toMediaCollection('cover');
        }
        if ($request->input('logo', false)) {
            $service->addMedia(storage_path('tmp/uploads/' . basename($request->input('logo'))))->toMediaCollection('logo');
        }

        return redirect()->route('admin.services.index');
    }

    public function edit(Service $service)
    {
        abort_if(Gate::denies('service_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $service_types = ServiceType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $image_service_id = MobileSetting::all()->first()->classes_service_type;
        $service->load('service_type');

        return view('admin.services.edit', compact('service_types', 'service','image_service_id'));
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service->update($request->all());
        if ($request->input('cover', false)) {
            if (!$service->cover || $request->input('cover') !== $service->cover->file_name) {
                if ($service->cover) {
                    $service->cover->delete();
                }
                $service->addMedia(storage_path('tmp/uploads/' . basename($request->input('cover'))))->toMediaCollection('cover');
            }
        } elseif ($service->cover) {
            $service->cover->delete();
        }
        if ($request->input('logo', false)) {
            if (!$service->logo || $request->input('logo') !== $service->logo->file_name) {
                if ($service->logo) {
                    $service->logo->delete();
                }
                $service->addMedia(storage_path('tmp/uploads/' . basename($request->input('logo'))))->toMediaCollection('logo');
            }
        } elseif ($service->logo) {
            $service->logo->delete();
        }

        return redirect()->route('admin.services.index');
    }

    public function show(Service $service)
    {
        abort_if(Gate::denies('service_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $service->load('service_type');

        return view('admin.services.show', compact('service'));
    }

    public function destroy(Service $service)
    {
        abort_if(Gate::denies('service_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $service->delete();

        return back();
    }

    public function massDestroy(MassDestroyServiceRequest $request)
    {
        Service::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getPricelistByServiceType($id)
    {
        $service_type = ServiceType::with(['service_pricelists' => fn($q) => $q->where('pricelists.status','active')])->findOrFail($id);

        return response()->json([
            'service_type'      => $service_type,
            'pricelists'        => $service_type->service_pricelists,
        ]);
    }
}
