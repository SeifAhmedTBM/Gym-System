<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyAssetTypeRequest;
use App\Http\Requests\StoreAssetTypeRequest;
use App\Http\Requests\UpdateAssetTypeRequest;
use App\Models\AssetType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class AssetTypesController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('asset_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = AssetType::query()->select(sprintf('%s.*', (new AssetType())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'asset_type_show';
                $editGate = 'asset_type_edit';
                $deleteGate = 'asset_type_delete';
                $crudRoutePart = 'asset-types';

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

        return view('admin.assetTypes.index');
    }

    public function create()
    {
        abort_if(Gate::denies('asset_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.assetTypes.create');
    }

    public function store(StoreAssetTypeRequest $request)
    {
        $assetType = AssetType::create($request->all());

        return redirect()->route('admin.asset-types.index');
    }

    public function edit(AssetType $assetType)
    {
        abort_if(Gate::denies('asset_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.assetTypes.edit', compact('assetType'));
    }

    public function update(UpdateAssetTypeRequest $request, AssetType $assetType)
    {
        $assetType->update($request->all());

        return redirect()->route('admin.asset-types.index');
    }

    public function show(AssetType $assetType)
    {
        abort_if(Gate::denies('asset_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.assetTypes.show', compact('assetType'));
    }

    public function destroy(AssetType $assetType)
    {
        abort_if(Gate::denies('asset_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assetType->delete();

        return back();
    }

    public function massDestroy(MassDestroyAssetTypeRequest $request)
    {
        AssetType::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
