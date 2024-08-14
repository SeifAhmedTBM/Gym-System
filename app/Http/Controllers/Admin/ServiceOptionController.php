<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyServiceOptionRequest;
use App\Http\Requests\StoreServiceOptionRequest;
use App\Http\Requests\UpdateServiceOptionRequest;
use App\Models\ServiceOption;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ServiceOptionController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('service_option_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ServiceOption::query()->select(sprintf('%s.*', (new ServiceOption())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'service_option_show';
                $editGate = 'service_option_edit';
                $deleteGate = 'service_option_delete';
                $crudRoutePart = 'service-options';

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
            
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.serviceOptions.index');
    }

    public function create()
    {
        abort_if(Gate::denies('service_option_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.serviceOptions.create');
    }

    public function store(StoreServiceOptionRequest $request)
    {
        $serviceOption = ServiceOption::create($request->all());

        return redirect()->route('admin.service-options.index');
    }

    public function edit(ServiceOption $serviceOption)
    {
        abort_if(Gate::denies('service_option_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.serviceOptions.edit', compact('serviceOption'));
    }

    public function update(UpdateServiceOptionRequest $request, ServiceOption $serviceOption)
    {
        $serviceOption->update($request->all());

        return redirect()->route('admin.service-options.index');
    }

    public function show(ServiceOption $serviceOption)
    {
        abort_if(Gate::denies('service_option_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.serviceOptions.show', compact('serviceOption'));
    }

    public function destroy(ServiceOption $serviceOption)
    {
        abort_if(Gate::denies('service_option_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $serviceOption->delete();

        return back();
    }

    public function massDestroy(MassDestroyServiceOptionRequest $request)
    {
        ServiceOption::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
