<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyServiceTypeRequest;
use App\Http\Requests\StoreServiceTypeRequest;
use App\Http\Requests\UpdateServiceTypeRequest;
use App\Models\ServiceType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ServiceTypesController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('service_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ServiceType::query()->select(sprintf('%s.*', (new ServiceType())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'service_type_show';
                $editGate = 'service_type_edit';
                $deleteGate = 'service_type_delete';
                $crudRoutePart = 'service-types';
                $St = true;

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'St',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });

            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });

            $table->editColumn('main_service', function ($row) {
                return \App\Models\ServiceType::MAIN_SERVICE[$row->main_service];
            });

            $table->addColumn('is_pt', function ($row) {
                return  $row->is_pt == true ? '<span class="badge badge-success p-2">' . ServiceType::IS_PT[$row->is_pt] . '</span>' : '<span class="badge badge-danger p-2">' . ServiceType::IS_PT[$row->is_pt] . '</span>';
            });

            $table->addColumn('isClass', function ($row) {
                return  $row->isClass == true ? '<span class="badge badge-success p-2">' . ServiceType::IS_CLASS[$row->isClass] . '</span>' : '<span class="badge badge-danger p-2">' . ServiceType::IS_CLASS[$row->isClass] . '</span>';
            });

            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });

            $table->editColumn('session_type', function ($row) {
                return $row->session_type ? $row->session_type : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'is_pt' ,'isClass']);

            return $table->make(true);
        }

        return view('admin.serviceTypes.index');
    }

    public function create()
    {
       
        abort_if(Gate::denies('service_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.serviceTypes.create');
    }

    public function store(StoreServiceTypeRequest $request)
    {

        $serviceType = ServiceType::create([
            'name'          => $request['name'],
            'description'   => $request['description'],
            'session_type'  => $request['session_type'],
            'main_service'  => isset($request['main_service']) ? true : false,
            'is_pt'  => isset($request['is_pt']) ? true : false ,
            'isClass'  => isset($request['isClass']) ? true : false ,
        ]);

        return redirect()->route('admin.service-types.index');
    }

    public function edit(ServiceType $serviceType)
    {
        abort_if(Gate::denies('service_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.serviceTypes.edit', compact('serviceType'));
    }

    public function update(UpdateServiceTypeRequest $request, ServiceType $serviceType)
    {
        $serviceType->update([
            'name'          => $request['name'],
            'description'   => $request['description'],
            'session_type'  => $request['session_type'],
            'main_service'  => isset($request['main_service']) ? true : false,
            'is_pt'  => isset($request['is_pt']) ? true : false ,
            'isClass'  => isset($request['isClass']) ? true : false ,
        ]);

        return redirect()->route('admin.service-types.index');
    }

    public function show(ServiceType $serviceType)
    {
        abort_if(Gate::denies('service_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.serviceTypes.show', compact('serviceType'));
    }

    public function destroy(ServiceType $serviceType)
    {
        abort_if(Gate::denies('service_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $serviceType->delete();

        return back();
    }

    public function massDestroy(MassDestroyServiceTypeRequest $request)
    {
        ServiceType::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
