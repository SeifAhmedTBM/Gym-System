<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyMaintenanceVendorRequest;
use App\Http\Requests\StoreMaintenanceVendorRequest;
use App\Http\Requests\UpdateMaintenanceVendorRequest;
use App\Models\MaintenanceVendor;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class MaintenanceVendorsController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('maintenance_vendor_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = MaintenanceVendor::query()->select(sprintf('%s.*', (new MaintenanceVendor())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'maintenance_vendor_show';
                $editGate = 'maintenance_vendor_edit';
                $deleteGate = 'maintenance_vendor_delete';
                $crudRoutePart = 'maintenance-vendors';

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

            $table->editColumn('mobile', function ($row) {
                return $row->mobile ? $row->mobile : '';
            });

            $table->editColumn('notes', function ($row) {
                return $row->notes ? $row->notes : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.maintenanceVendors.index');
    }

    public function create()
    {
        abort_if(Gate::denies('maintenance_vendor_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.maintenanceVendors.create');
    }

    public function store(StoreMaintenanceVendorRequest $request)
    {
        $maintenanceVendor = MaintenanceVendor::create($request->all());

        return redirect()->route('admin.maintenance-vendors.index');
    }

    public function edit(MaintenanceVendor $maintenanceVendor)
    {
        abort_if(Gate::denies('maintenance_vendor_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.maintenanceVendors.edit', compact('maintenanceVendor'));
    }

    public function update(UpdateMaintenanceVendorRequest $request, MaintenanceVendor $maintenanceVendor)
    {
        $maintenanceVendor->update($request->all());

        return redirect()->route('admin.maintenance-vendors.index');
    }

    public function show(MaintenanceVendor $maintenanceVendor)
    {
        abort_if(Gate::denies('maintenance_vendor_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.maintenanceVendors.show', compact('maintenanceVendor'));
    }

    public function destroy(MaintenanceVendor $maintenanceVendor)
    {
        abort_if(Gate::denies('maintenance_vendor_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $maintenanceVendor->delete();

        return back();
    }

    public function massDestroy(MassDestroyMaintenanceVendorRequest $request)
    {
        MaintenanceVendor::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
