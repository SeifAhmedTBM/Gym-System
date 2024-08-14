<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyAssetsMaintenanceRequest;
use App\Http\Requests\StoreAssetsMaintenanceRequest;
use App\Http\Requests\UpdateAssetsMaintenanceRequest;
use App\Models\Asset;
use App\Models\AssetsMaintenance;
use App\Models\Expense;
use App\Models\ExpensesCategory;
use App\Models\MaintenanceVendor;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class AssetsMaintenanceController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('assets_maintenance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = AssetsMaintenance::with(['asset', 'maintence_vendor'])->select(sprintf('%s.*', (new AssetsMaintenance())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'assets_maintenance_show';
                $editGate = 'assets_maintenance_edit';
                $deleteGate = 'assets_maintenance_delete';
                $crudRoutePart = 'assets-maintenances';

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

            $table->editColumn('amount', function ($row) {
                return $row->amount ? $row->amount : '';
            });

            $table->editColumn('account', function ($row) {
                return $row->account_id != NULL ? $row->account->name : trans('global.no_data_available');
            });
            $table->editColumn('comment', function ($row) {
                return $row->comment ? $row->comment : trans('global.no_data_available');
            });
            $table->addColumn('asset_name', function ($row) {
                return $row->asset ? $row->asset->name : '';
            });

            $table->addColumn('maintence_vendor_name', function ($row) {
                return $row->maintence_vendor ? $row->maintence_vendor->name : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'asset', 'maintence_vendor']);

            return $table->make(true);
        }

        $assetsMaintenances = AssetsMaintenance::get();

        return view('admin.assetsMaintenances.index',compact('assetsMaintenances'));
    }

    public function create()
    {
        abort_if(Gate::denies('assets_maintenance_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assets = Asset::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $maintence_vendors = MaintenanceVendor::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.assetsMaintenances.create', compact('assets', 'maintence_vendors'));
    }

    public function store(StoreAssetsMaintenanceRequest $request)
    {
        AssetsMaintenance::create($request->all());

        Expense::create([
            'name'      => 'Asset Maintenance',
            'date'      => $request['date'],
            'amount'    => $request['amount'],
            'expenses_category_id'  => ExpensesCategory::firstOrCreate(['name' => 'Asset Maintenance'])->id,
            'created_by_id'     => auth()->id(),
            'account_id'        => $request['account_id']
        ]);

        return redirect()->route('admin.assets-maintenances.index');
    }

    public function edit(AssetsMaintenance $assetsMaintenance)
    {
        abort_if(Gate::denies('assets_maintenance_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assets = Asset::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $maintence_vendors = MaintenanceVendor::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $assetsMaintenance->load('asset', 'maintence_vendor');

        return view('admin.assetsMaintenances.edit', compact('assets', 'maintence_vendors', 'assetsMaintenance'));
    }

    public function update(UpdateAssetsMaintenanceRequest $request, AssetsMaintenance $assetsMaintenance)
    {
        $assetsMaintenance->update($request->all());

        return redirect()->route('admin.assets-maintenances.index');
    }

    public function show(AssetsMaintenance $assetsMaintenance)
    {
        abort_if(Gate::denies('assets_maintenance_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assetsMaintenance->load('asset', 'maintence_vendor');

        return view('admin.assetsMaintenances.show', compact('assetsMaintenance'));
    }

    public function destroy(AssetsMaintenance $assetsMaintenance)
    {
        abort_if(Gate::denies('assets_maintenance_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assetsMaintenance->delete();

        return back();
    }

    public function massDestroy(MassDestroyAssetsMaintenanceRequest $request)
    {
        AssetsMaintenance::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
