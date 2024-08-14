<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\WarehouseProduct;
use Illuminate\Support\Facades\DB;
use App\Models\ProductTransactions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\StoreWarehouseProductRequest;
use App\Http\Requests\UpdateWarehouseProductRequest;
use App\Http\Requests\MassDestroyWarehouseProductRequest;

class WarehouseProductsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('warehouse_product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $warehouseProducts = WarehouseProduct::with(['product', 'warehouse'])->get();

        return view('admin.warehouseProducts.index', compact('warehouseProducts'));
    }

    public function create()
    {
        abort_if(Gate::denies('warehouse_product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = Product::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $warehouses = Warehouse::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.warehouseProducts.create', compact('products', 'warehouses'));
    }

    public function store(StoreWarehouseProductRequest $request)
    {
        $warehouseProduct = WarehouseProduct::create([
            'warehouse_id' => $request->warehouse_id,
            'product_id' => $request->product_id,
            'balance' => $request->balance,
        ]);

        return back();
    }

    public function edit(WarehouseProduct $warehouseProduct)
    {
        abort_if(Gate::denies('warehouse_product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = Product::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $warehouses = Warehouse::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $warehouseProduct->load('product', 'warehouse');

        return view('admin.warehouseProducts.edit', compact('products', 'warehouseProduct', 'warehouses'));
    }

    public function update(UpdateWarehouseProductRequest $request, WarehouseProduct $warehouseProduct)
    {   

        // DB::transaction(function () use ($request, $warehouseProduct) {
            $warehouseProduct->update([
                'balance' => $request->type == 'in' ? $warehouseProduct->balance += $request->quantity : $warehouseProduct->balance -= $request->quantity,
            ]);
    
            $transaction = ProductTransactions::create([
                'from_warehouse'    => $request->from_warehouse,
                'to_warehouse'      => $request->to_warehouse,
                'product_id'        => $warehouseProduct->product_id,
                'quantity'          => $request->quantity,
                'type'              => $request->type,
                'created_by'        => Auth()->user()->id,
                'notes'             => $request->notes,
            ]);
    
            if($request->type == 'transfer')
            {
                $warehouse          = Warehouse::findOrFail($transaction->to_warehouse);
                $warehouseProducts  = WarehouseProduct::whereWarehouseId($warehouse->id)->whereProductId($transaction->product_id)->first();

                // return $warehouseProducts;

                if (is_null($warehouseProducts)) 
                {
                   WarehouseProduct::create([
                       'product_id'         => $transaction->product_id,
                       'warehouse_id'       => $warehouse->id
                   ]);
                }

                $warehouseProducts->update([
                    'balance'       => $warehouseProducts->balance += $request->quantity
                ],[
                    'warehouse_id'  => $transaction->to_warehouse,
                    'product_id'    => $transaction->product_id,
                    'balance'       => $request->quantity,
                ]);
            }
            $this->sent_successfully();
        // });
        

        return redirect()->route('admin.warehouses.show',$warehouseProduct->warehouse_id);
    }

    public function show(WarehouseProduct $warehouseProduct)
    {
        abort_if(Gate::denies('warehouse_product_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $warehouseProduct->load('product', 'warehouse');

        return view('admin.warehouseProducts.show', compact('warehouseProduct'));
    }

    public function destroy(WarehouseProduct $warehouseProduct)
    {
        abort_if(Gate::denies('warehouse_product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $warehouseProduct->delete();

        return back();
    }

    public function massDestroy(MassDestroyWarehouseProductRequest $request)
    {
        WarehouseProduct::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getWarehouseProduct($id)
    {
        $warehouseProduct = WarehouseProduct::with('product')->findOrFail($id);

        return response()->json([
            'warehouseProduct' => $warehouseProduct
        ]);
    }
}
