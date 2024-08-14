<?php

namespace App\Http\Controllers\Admin;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreWarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\MassDestroyWarehouseRequest;
use App\Models\Product;

class WarehouseController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('warehouse_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $warehouses = Warehouse::all();

        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        abort_if(Gate::denies('warehouse_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.warehouses.create');
    }

    public function store(StoreWarehouseRequest $request)
    {
        $warehouse = Warehouse::create($request->all());

        return redirect()->route('admin.warehouses.index');
    }

    public function edit(Warehouse $warehouse)
    {
        abort_if(Gate::denies('warehouse_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.warehouses.edit', compact('warehouse'));
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse)
    {
        $warehouse->update($request->all());

        return redirect()->route('admin.warehouses.index');
    }

    public function show(Warehouse $warehouse)
    {
        abort_if(Gate::denies('warehouse_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $products = Product::whereNotIn('id',$warehouse->warehouseProducts()->get('product_id'))->pluck('name','id');

        $warehouses = Warehouse::where('id','!=',$warehouse->id)->pluck('name','id');

        return view('admin.warehouses.show', compact('warehouse','products','warehouses'));
    }

    public function destroy(Warehouse $warehouse)
    {
        abort_if(Gate::denies('warehouse_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $warehouse->delete();

        return back();
    }

    public function massDestroy(MassDestroyWarehouseRequest $request)
    {
        Warehouse::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
