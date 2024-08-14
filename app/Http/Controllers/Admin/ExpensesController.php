<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Branch;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Exports\ExpensesExport;
use App\Models\ExpensesCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyExpenseRequest;

class ExpensesController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('expense_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->except(['draw', 'columns', 'order', 'start', 'length', 'search', 'change_language','_']);

        $employee = Auth()->user()->employee;

        if ($request->ajax()) {
            if ($employee && $employee->branch_id != NULL) 
            {
                $query = Expense::index($data)
                                    ->with(['expenses_category', 'created_by','account'])
                                    ->whereHas('account',fn($q) => $q->whereBranchId($employee->branch_id))
                                    ->select(sprintf('%s.*', (new Expense())->table));
            }else{
                $query = Expense::index($data)
                                    ->with(['expenses_category', 'created_by','account.branch'])
                                    ->select(sprintf('%s.*', (new Expense())->table));
            }

            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'expense_show';
                $editGate = 'expense_edit';
                $deleteGate = 'expense_delete';
                $crudRoutePart = 'expenses';

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

            $table->addColumn('account_name', function ($row) {
                return $row->account_id ? $row->account->name : '';
            });

            $table->editColumn('amount', function ($row) {
                return $row->amount ? $row->amount : '';
            });

            $table->addColumn('expenses_category_name', function ($row) {
                return $row->expenses_category ? $row->expenses_category->name : '';
            });

            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->addColumn('branch_name', function ($row) {
                return $row->account && $row->account->branch ? $row->account->branch->name : '-';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->toFormattedDateString() . ' , ' . $row->created_at->format('g:i A') : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'expenses_category', 'created_by','account_name','branch_name']);

            return $table->make(true);
        }

        $expenses_categories = ExpensesCategory::pluck('name', 'id');

        $accounts = Account::orderBy('name')->pluck('name','id');

        $users = User::whereHas('employee')->orderBy('name')->pluck('name','id');

        $branches = Branch::pluck('name','id');

        if ($employee && $employee->branch_id != NULL) 
        {
            $expenses = Expense::index($data)->whereHas('account',fn($q) => $q->whereBranchId($employee->branch_id));
        }else{
            $expenses = Expense::index($data);
        }
        
        return view('admin.expenses.index',compact('expenses_categories','users','accounts','expenses','branches'));
    }

    public function create()
    {
        abort_if(Gate::denies('expense_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expenses_categories = ExpensesCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $branches = Branch::pluck('name','id')->prepend(trans('global.pleaseSelect'),'');

        $selected_branch = Auth()->user()->employee->branch ?? NULL;

        $accounts = Account::pluck('name','id');

        return view('admin.expenses.create', compact('expenses_categories', 'created_bies','branches','selected_branch','accounts'));
    }

    public function store(StoreExpenseRequest $request)
    {
        $expense = Expense::create([
            'name' => $request->name,
            'date' => $request->date,
            'amount' => $request->amount,
            'note' => $request->note,
            'expenses_category_id' => $request->expenses_category_id,
            'account_id' => $request->account_id,
            'created_at' => $request->date,
            'created_by_id' => auth()->user()->id,
        ]);

        $expense->account->balance = $expense->account->balance - $expense->amount;
        $expense->account->save();

        $transaction = Transaction::create([
            'transactionable_type' => 'App\\Models\\Expense',
            'transactionable_id' => $expense->id,
            'amount' => $expense->amount,
            'account_id' => $expense->account_id,
            'created_at' => $request->date,
            'created_by' => auth()->user()->id,
        ]);

        return redirect()->route('admin.expenses.index');
    }

    public function edit(Expense $expense)
    {
        abort_if(Gate::denies('expense_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expenses_categories = ExpensesCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $expense->load('expenses_category', 'created_by');

        $selected_branch = Auth()->user()->employee->branch ?? NULL;

        if(is_null($selected_branch)){

            $accounts = Account::pluck('name','id');

        }else{

            $accounts = Account::where('branch_id',$selected_branch->id)->pluck('name','id');
            
        }

        return view('admin.expenses.edit', compact('expenses_categories', 'created_bies', 'expense','accounts','selected_branch'));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $expense->account->balance = $expense->account->balance + $expense->amount;
        $expense->account->save();

        $expense->transaction->update([
            'created_at'    => $request['date'].date('H:i:s'),
            'account_id'    => $request['account_id'],
            'amount'        => $request['amount']
        ]);
        $expense->created_at = $request['date'];
        $expense->save();
        
        $expense->update($request->all());
        
        $expense->account->balance = $expense->account->balance - $request['amount'];
        $expense->account->save();

        return redirect()->route('admin.expenses.index');
    }

    public function show(Expense $expense)
    {
        abort_if(Gate::denies('expense_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expense->load('expenses_category', 'created_by');

        return view('admin.expenses.show', compact('expense'));
    }

    public function destroy(Expense $expense)
    {
        abort_if(Gate::denies('expense_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expense->account->balance = $expense->account->balance + $expense->amount;
        $expense->account->save();

        $expense->delete();

        return back();
    }

    public function massDestroy(MassDestroyExpenseRequest $request)
    {
        Expense::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function export(Request $request)
    {
        return Excel::download(new ExpensesExport($request), 'Expenses.xlsx');
    }
}
