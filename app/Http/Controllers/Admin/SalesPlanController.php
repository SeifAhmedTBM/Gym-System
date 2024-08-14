<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroySalesPlanRequest;
use App\Http\Requests\StoreSalesPlanRequest;
use App\Http\Requests\UpdateSalesPlanRequest;
use App\Models\SalesPlan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class SalesPlanController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('sales_plan_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = SalesPlan::query()->select(sprintf('%s.*', (new SalesPlan())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'sales_plan_show';
                $editGate = 'sales_plan_edit';
                $deleteGate = 'sales_plan_delete';
                $crudRoutePart = 'sales-plans';

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

        return view('admin.salesPlans.index');
    }

    public function create()
    {
        abort_if(Gate::denies('sales_plan_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.salesPlans.create');
    }

    public function store(StoreSalesPlanRequest $request)
    {
        $salesPlan = SalesPlan::create($request->all());

        return redirect()->route('admin.sales-plans.index');
    }

    public function edit(SalesPlan $salesPlan)
    {
        abort_if(Gate::denies('sales_plan_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.salesPlans.edit', compact('salesPlan'));
    }

    public function update(UpdateSalesPlanRequest $request, SalesPlan $salesPlan)
    {
        $salesPlan->update($request->all());

        return redirect()->route('admin.sales-plans.index');
    }

    public function show(SalesPlan $salesPlan)
    {
        abort_if(Gate::denies('sales_plan_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.salesPlans.show', compact('salesPlan'));
    }

    public function destroy(SalesPlan $salesPlan)
    {
        abort_if(Gate::denies('sales_plan_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesPlan->delete();

        return back();
    }

    public function massDestroy(MassDestroySalesPlanRequest $request)
    {
        SalesPlan::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
