<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyExpensesCategoryRequest;
use App\Http\Requests\StoreExpensesCategoryRequest;
use App\Http\Requests\UpdateExpensesCategoryRequest;
use App\Models\ExpensesCategory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ExpensesCategoryController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('expenses_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ExpensesCategory::query()->select(sprintf('%s.*', (new ExpensesCategory())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'expenses_category_show';
                $editGate = 'expenses_category_edit';
                $deleteGate = 'expenses_category_delete';
                $crudRoutePart = 'expenses-categories';

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

        return view('admin.expensesCategories.index');
    }

    public function create()
    {
        abort_if(Gate::denies('expenses_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.expensesCategories.create');
    }

    public function store(StoreExpensesCategoryRequest $request)
    {
        $expensesCategory = ExpensesCategory::create($request->all());

        return redirect()->route('admin.expenses-categories.index');
    }

    public function edit(ExpensesCategory $expensesCategory)
    {
        abort_if(Gate::denies('expenses_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.expensesCategories.edit', compact('expensesCategory'));
    }

    public function update(UpdateExpensesCategoryRequest $request, ExpensesCategory $expensesCategory)
    {
        $expensesCategory->update($request->all());

        return redirect()->route('admin.expenses-categories.index');
    }

    public function show(ExpensesCategory $expensesCategory)
    {
        abort_if(Gate::denies('expenses_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.expensesCategories.show', compact('expensesCategory'));
    }

    public function destroy(ExpensesCategory $expensesCategory)
    {
        abort_if(Gate::denies('expenses_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expensesCategory->delete();

        return back();
    }

    public function massDestroy(MassDestroyExpensesCategoryRequest $request)
    {
        ExpensesCategory::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
