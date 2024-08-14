<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroySalesIntensiveRequest;
use App\Http\Requests\StoreSalesIntensiveRequest;
use App\Http\Requests\UpdateSalesIntensiveRequest;
use App\Models\SalesIntensive;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class SalesIntensiveController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('sales_intensive_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = SalesIntensive::query()->select(sprintf('%s.*', (new SalesIntensive())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'sales_intensive_show';
                $editGate = 'sales_intensive_edit';
                $deleteGate = 'sales_intensive_delete';
                $crudRoutePart = 'sales-intensives';

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

        return view('admin.salesIntensives.index');
    }

    public function create()
    {
        abort_if(Gate::denies('sales_intensive_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.salesIntensives.create');
    }

    public function store(StoreSalesIntensiveRequest $request)
    {
        $salesIntensive = SalesIntensive::create($request->all());

        return redirect()->route('admin.sales-intensives.index');
    }

    public function edit(SalesIntensive $salesIntensive)
    {
        abort_if(Gate::denies('sales_intensive_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.salesIntensives.edit', compact('salesIntensive'));
    }

    public function update(UpdateSalesIntensiveRequest $request, SalesIntensive $salesIntensive)
    {
        $salesIntensive->update($request->all());

        return redirect()->route('admin.sales-intensives.index');
    }

    public function show(SalesIntensive $salesIntensive)
    {
        abort_if(Gate::denies('sales_intensive_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.salesIntensives.show', compact('salesIntensive'));
    }

    public function destroy(SalesIntensive $salesIntensive)
    {
        abort_if(Gate::denies('sales_intensive_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesIntensive->delete();

        return back();
    }

    public function massDestroy(MassDestroySalesIntensiveRequest $request)
    {
        SalesIntensive::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
